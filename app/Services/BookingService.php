<?php

namespace App\Services;

use App\Enums\BookingDetailStatus;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\InvoiceDetail;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class BookingService
{
    public function __construct(
        protected Booking $model,
        protected User $user,
        protected Room $room,
        protected BookingDetail $bookingDetail
    ) {}

    /**
     * Get a list of bookings filtered by the given payload.
     *
     * @param array $payload
     */
    public function get($payload)
    {
        $per_page = Arr::get($payload, 'per_page', 15);

        $query = $this->model->newQuery();

        $query = $this->filter($payload);

        $month = Arr::get($payload, 'month');
        $year = Arr::get($payload, 'year');

        if ($month && $year) {
            $startDateOfMonth = Carbon::createFromDate($year, $month, 1)->startOfDay();
            $endDateOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();

            $query->whereHas('bookingDetails', function ($q) use ($startDateOfMonth, $endDateOfMonth) {
                $q->where(function ($innerQ) use ($startDateOfMonth, $endDateOfMonth) {
                    $innerQ->whereBetween('checkin_at', [$startDateOfMonth, $endDateOfMonth])
                        ->whereBetween('checkout_at', [$startDateOfMonth, $endDateOfMonth])
                        ->orWhere(function ($q2) use ($startDateOfMonth, $endDateOfMonth) {
                            $q2->where('checkin_at', '<', $startDateOfMonth)
                                ->whereBetween('checkout_at', [$startDateOfMonth, $endDateOfMonth->copy()->addDay()]);
                        })
                        ->orWhere(function ($q3) use ($startDateOfMonth, $endDateOfMonth) {
                            $q3->whereBetween('checkin_at', [$startDateOfMonth, $endDateOfMonth])
                                ->where('checkout_at', '>', $endDateOfMonth);
                        })
                        ->orWhere(function ($q4) use ($startDateOfMonth, $endDateOfMonth) {
                            $q4->where('checkin_at', '<', $startDateOfMonth)
                                ->where('checkout_at', '>', $endDateOfMonth);
                        });
                });
            });
        }

        $query->orderBy('created_at', 'desc');

        $query->with('bookingDetails');

        return $query->paginate($per_page);
    }

    /**
     * Get all bookings.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCustomer(): Collection
    {
        $user = Auth::user();

        $query  = $this->model->where('user_id', $user->id);

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Filter bookings based on specified criteria.
     *
     * @param array $filter An associative array containing filter criteria such as
     *                      'guest_name', 'guest_email', 'guest_phone', and 'status'.
     * @return \Illuminate\Database\Eloquent\Builder The query builder with applied filters.
     */
    public function filter($filter)
    {
        return $this->model
            ->when(Arr::get($filter, 'guest_name'), function ($query, $guest_name) {
                $query->where('guest_name', 'like', '%' . $guest_name . '%');
            })
            ->when(Arr::get($filter, 'guest_email'), function ($query, $guest_email) {
                $query->where('guest_email', 'like', '%' . $guest_email . '%');
            })
            ->when(Arr::get($filter, 'guest_phone'), function ($query, $guest_phone) {
                $query->where('guest_phone', 'like', '%' . $guest_phone . '%');
            })
            ->when(Arr::get($filter, 'status'), function ($query, $status) {
                $query->where('status', $status);
            });
    }
    /**
     * Create a booking.
     *
     * @param array $data
     * @return \App\Models\Booking
     */
    public function create($data): Booking
    {
        $this->validateRoomAvailability($data['booking_details']);

        return DB::transaction(function () use ($data) {
            $status = Arr::has($data, 'user_id') && $data['user_id'] ? BookingStatus::PENDING : BookingStatus::CONFIRMED;
            $booking = [
                'user_id'        => Arr::get($data, 'user_id'),
                'guest_name'     => Arr::get($data, 'guest_name'),
                'guest_email'    => Arr::get($data, 'guest_email'),
                'guest_phone'    => Arr::get($data, 'guest_phone'),
                'deposit'        => Arr::get($data, 'deposit'),
                'status'         => $status,
                'note'           => Arr::get($data, 'note'),
            ];

            $total = 0;

            $rooms = $this->getRoomsByBookingDetails($data['booking_details']);

            $bookingDetails = collect($data['booking_details'])->map(function ($item) use (&$total, $rooms) {
                $price = $this->getPriceRoomType($rooms, $item['room_id']);

                $duration = config('room.duration');
                $unitHours = config('room.unit_hours');
                $cleanRoom = config('room.clean_room');

                $duration = $this->calculateDuration($item['checkin_at'], $item['checkout_at'], $unitHours);

                $itemTotal = $duration * $price;
                $total += $itemTotal;

                $item['price_per_day'] = $price;
                $item['status'] = BookingDetailStatus::PENDING;
                $item['checkout_at'] = Carbon::parse($item['checkout_at'])->addMinutes($cleanRoom);
                $item['id'] = Arr::get($item, 'id');

                return $item;
            })->toArray();

            $booking['total_payment'] = $total;

            $recordBooking = $this->model->create($booking);

            $recordBooking->bookingDetails()->createMany($bookingDetails);

            return $recordBooking;
        });
    }

    /**
     * Find a booking by its ID.
     *
     * @param  int  $id  The booking ID.
     * @return \App\Models\Booking
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById($id): Booking
    {
        $booking = $this->model->with([
            'bookingDetails.room.roomType',
        ])->findOrFail($id);

        return $booking;
    }

    /**
     * Update a booking.
     *
     * @param  array  $data
     * @param  int  $id
     * @return \App\Models\Booking
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update($data, $id)
    {
        $booking = $this->model->whereIn('status', [
            BookingStatus::PENDING,
            BookingStatus::CONFIRMED,
            BookingStatus::CHECK_IN,
        ])->findOrFail($id);

        $this->validateRoomAvailability($data['booking_details'], $booking->id);

        return DB::transaction(function () use ($booking, $data) {
            $bookingUpdate = [
                'user_id'        => Arr::get($data, 'user_id'),
                'guest_name'     => Arr::get($data, 'guest_name'),
                'guest_email'    => Arr::get($data, 'guest_email'),
                'guest_phone'    => Arr::get($data, 'guest_phone'),
                'deposit'        => Arr::get($data, 'deposit'),
                'note'           => Arr::get($data, 'note'),
            ];

            $hasNewDetails = collect($data['booking_details'])->contains(fn($value) => empty($value['id']));

            if ($hasNewDetails) {
                $bookingUpdate['status'] = BookingStatus::CONFIRMED;
            }

            $idBookingDetails = collect($data['booking_details'])->pluck('id')->filter()->toArray();

            // Check if any booking detail to be removed is already checked in
            $detailsToRemoveQuery = BookingDetail::where('booking_id', $booking->id);
            if (!empty($idBookingDetails)) { // If $idBookingDetails is empty, it means all details are to be removed
                $detailsToRemoveQuery->whereNotIn('id', $idBookingDetails);
            }

            $checkedInDetailExistsForRemoval = (clone $detailsToRemoveQuery)
                ->where('status', BookingDetailStatus::CHECK_IN)
                ->exists();

            if ($checkedInDetailExistsForRemoval) {
                throw new \Exception("Not delete room that is checked-in.");
            }

            // Proceed with deletion if no checked-in rooms are affected
            $detailsToRemoveQuery->delete();

            $rooms = $this->getRoomsByBookingDetails($data['booking_details']);
            $booking->load('bookingDetails');
            $existingDetails = $booking->bookingDetails->keyBy('id');

            $total = 0;

            $bookingDetails = collect($data['booking_details'])->map(function ($item) use (&$total, $rooms, $existingDetails) {
                $price = $this->getPriceRoomType($rooms, $item['room_id']);

                $duration = config('room.duration');
                $unitHours = config('room.unit_hours');
                $cleanRoom = config('room.clean_room');

                $duration = $this->calculateDuration($item['checkin_at'], $item['checkout_at'], $unitHours);

                $itemTotal = $duration * $price;
                $total += $itemTotal;

                $item['price_per_day'] = $price;

                $item['checkout_at'] = Carbon::parse($item['checkout_at'])->addMinutes($cleanRoom);

                $item['id'] = $item['id'] ?? null;

                if (!$item['id']) {
                    $item['status'] = BookingDetailStatus::PENDING;
                    $item['created_at'] = now();
                } else {
                    $existingDetail = $existingDetails->get($item['id']);
                    $item['status'] = $existingDetail->status;
                    $item['created_at'] = $existingDetail->created_at;
                }

                $item['updated_at'] = now();

                return $item;
            })->toArray();

            $bookingUpdate['total_payment'] = $total;

            $booking->update($bookingUpdate);
            $booking->bookingDetails()->upsert($bookingDetails, ['id']);

            return $booking;
        });
    }

    /**
     * Update a booking.
     *
     * @param  array  $data
     * @param  int  $id
     * @return \App\Models\Booking
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateClient($data, $id)
    {
        $booking = $this->model->where('status', BookingStatus::PENDING)->findOrFail($id);

        $this->validateRoomAvailability($data['booking_details'], $booking->id);

        return DB::transaction(function () use ($booking, $data) {
            // Lấy thông tin user từ bảng users
            $user = null;
            if (Arr::get($data, 'user_id')) {
                $user = $this->user->find(Arr::get($data, 'user_id'));
            }

            $bookingUpdate = [
                'user_id'        => Arr::get($data, 'user_id'),
                'guest_name'     => $user ? $user->name : Arr::get($data, 'guest_name'),
                'guest_email'    => $user ? $user->email : Arr::get($data, 'guest_email'),
                'guest_phone'    => $user ? $user->phone : Arr::get($data, 'guest_phone'),
                'deposit'        => Arr::get($data, 'deposit'),
                'note'           => Arr::get($data, 'note'),
            ];

            $idBookingDetails = collect($data['booking_details'])->pluck('id')->filter()->toArray();

            // Check if any booking detail to be removed is already checked in
            $detailsToRemoveQuery = BookingDetail::where('booking_id', $booking->id);
            if (!empty($idBookingDetails)) { // If $idBookingDetails is empty, it means all details are to be removed
                $detailsToRemoveQuery->whereNotIn('id', $idBookingDetails);
            }

            $checkedInDetailExistsForRemoval = (clone $detailsToRemoveQuery)
                ->where('status', BookingDetailStatus::CHECK_IN)
                ->exists();

            if ($checkedInDetailExistsForRemoval) {
                throw new \Exception("Not delete room that is checked-in.");
            }

            // Proceed with deletion if no checked-in rooms are affected
            $detailsToRemoveQuery->delete();

            $rooms = $this->getRoomsByBookingDetails($data['booking_details']);
            $booking->load('bookingDetails');
            $existingDetails = $booking->bookingDetails->keyBy('id');

            $total = 0;

            $bookingDetails = collect($data['booking_details'])->map(function ($item) use (&$total, $rooms, $existingDetails) {
                $price = $this->getPriceRoomType($rooms, $item['room_id']);

                $duration = config('room.duration');
                $unitHours = config('room.unit_hours');
                $cleanRoom = config('room.clean_room');

                $duration = $this->calculateDuration($item['checkin_at'], $item['checkout_at'], $unitHours);

                $itemTotal = $duration * $price;
                $total += $itemTotal;

                $item['price_per_day'] = $price;

                $item['checkout_at'] = Carbon::parse($item['checkout_at'])->addMinutes($cleanRoom);

                $item['id'] = $item['id'] ?? null;

                if (!$item['id']) {
                    $item['status'] = BookingDetailStatus::PENDING;
                    $item['created_at'] = now();
                } else {
                    $existingDetail = $existingDetails->get($item['id']);
                    $item['status'] = $existingDetail->status;
                    $item['created_at'] = $existingDetail->created_at;
                }

                $item['updated_at'] = now();

                return $item;
            })->toArray();

            $bookingUpdate['total_payment'] = $total;

            $booking->update($bookingUpdate);
            $booking->bookingDetails()->upsert($bookingDetails, ['id']);

            return $booking;
        });
    }

    /**
     * Find available rooms for a given date range.
     *
     * This method retrieves all rooms that are not occupied during the specified
     * check-in and check-out dates. It excludes rooms that have a booking with
     * one of the following statuses: pending, confirmed, or check-in.
     *
     * @param  array  $data  The input data.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function checkRoom($data): Collection
    {
        $newCheckIn = $data['checkin_at'];
        $newCheckOut = $data['checkout_at'];

        $occupiedRoomIds = $this->bookingDetail->whereHas('booking', function ($query) {
            $query->whereIn('status', [
                BookingStatus::PENDING,
                BookingStatus::CONFIRMED,
                BookingStatus::CHECK_IN
            ]);
        })
            ->where(function ($query) use ($newCheckIn, $newCheckOut) {
                $this->scopeTimeOverlap($query, $newCheckIn, $newCheckOut);
            })
            ->pluck('room_id')
            ->unique();

        $roomTypeId = Arr::get($data, 'roomType');
        $roomId = Arr::get($data, 'roomId');

        $query = $this->room->whereNotIn('id', $occupiedRoomIds);

        if ($roomId) {
            $availableRooms = new Collection([$query->findOrFail($roomId)]);
        } elseif ($roomTypeId) {
            $availableRooms = $query->where('room_type_id', $roomTypeId)->get();
        } else {
            $availableRooms = $query->get();
        }

        return $availableRooms;
    }

    /**
     * Retrieve rooms by booking details.
     *
     * This method takes an array of booking details and returns a collection of
     * rooms keyed by their ID. The rooms are eager loaded with their room type.
     *
     * @param  array  $bookingDetails  The booking details.
     * @return \Illuminate\Support\Collection
     */
    private function getRoomsByBookingDetails(array $bookingDetails)
    {
        $roomIds = collect($bookingDetails)->pluck('room_id')->unique();

        return $this->room->with('roomType')
            ->whereIn('id', $roomIds)
            ->get()
            ->keyBy('id');
    }

    /**
     * Retrieve the price of a room type given the room ID.
     *
     * This method takes a collection of rooms and a room ID, and returns the
     * price of the room type associated with the room.
     *
     * @param  \Illuminate\Support\Collection  $rooms  The collection of rooms.
     * @param  int  $roomId  The room ID.
     * @return float
     */
    private function getPriceRoomType($rooms, $roomId): float
    {
        $room = $rooms->get($roomId);

        return $room->roomType->price;
    }

    /**
     * Validate room availability for the given booking details.
     *
     * This method iterates over the booking details and checks if the room is
     * available for the selected dates. It excludes bookings with one of the
     * following statuses: pending, confirmed, or check-in. If the room is not
     * available, it throws an exception with a descriptive message.
     *
     * @param  array  $bookingDetails  The booking details.
     * @param  int|null  $excludeBookingId  The booking ID to exclude from the check.
     * @return void
     * @throws \Exception
     */
    private function validateRoomAvailability(array $bookingDetails, $excludeBookingId = null): void
    {
        foreach ($bookingDetails as $detail) {
            $roomId = $detail['room_id'];
            $newCheckIn = $detail['checkin_at'];
            $newCheckOut = $detail['checkout_at'];
            $room = $this->room::find($roomId);
            if (!$room) {
                throw new \Exception("Room with ID {$roomId} not found.");
            }

            $query = $this->bookingDetail->where('room_id', $roomId)
                ->whereHas('booking', function ($query) {
                    $query->whereIn('status', [
                        BookingStatus::PENDING,
                        BookingStatus::CONFIRMED,
                        BookingStatus::CHECK_IN
                    ]);
                })
                ->where(function ($query) use ($newCheckIn, $newCheckOut) {
                    $this->scopeTimeOverlap($query, $newCheckIn, $newCheckOut);
                });

            if ($excludeBookingId) {
                $query->where('booking_id', '!=', $excludeBookingId);
            }

            $isAvailable = !$query->exists();

            if (!$isAvailable) {
                throw new \Exception("Room {$room->name} is not available for the selected dates.");
            }
        }
    }

    /**
     * Calculate the duration of a booking in terms of the given time unit.
     *
     * This method takes the check-in and check-out times of a booking and
     * returns the duration of the booking in terms of the given time unit.
     * If the check-in or check-out times are empty, the method returns 1.
     *
     * @param  string  $checkin  The check-in time.
     * @param  string  $checkout  The check-out time.
     * @param  int  $unitHours  The time unit in hours. Defaults to 12.
     * @return int
     */
    private function calculateDuration($checkin, $checkout, $unitHours = 12): int
    {
        if (empty($checkin) || empty($checkout)) {
            return 1;
        }

        $checkin = Carbon::parse($checkin);
        $checkout = Carbon::parse($checkout);
        $diffInHours = $checkin->diffInHours($checkout, false);

        return $diffInHours > 0 ? max(1, ceil($diffInHours / $unitHours)) : 1;
    }

    /**
     * Scope a query to only include overlapping time ranges.
     *
     * This scope takes a start and end time, and returns a query that only
     * includes records that have a check-in time before the end time and a
     * check-out time after the start time.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $start  The start time.
     * @param  string  $end  The end time.
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function scopeTimeOverlap($query, $start, $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->where('checkin_at', '<', $end)
                ->where('checkout_at', '>', $start);
        });
    }

    /**
     * Delete a booking.
     *
     * This method deletes a booking by its ID. The booking must have a status
     * of CHECK_OUT, CANCELLED, NO_SHOW, or EXPIRED. If the booking is not found
     * or its status is not one of the above, an exception is thrown.
     *
     * @param  int  $id  The ID of the booking to be deleted.
     * @return bool  True if the booking was successfully deleted, false otherwise.
     * @throws \Exception
     */
    public function delete($id): bool
    {
        return DB::transaction(function () use ($id) {
            $booking = $this->model->where('id', $id)
                ->whereIn('status', [
                    BookingStatus::CANCELLED,
                ])
                ->first();

            if (!$booking) {
                throw new \Exception("Booking can't delete.");
            }

            $this->bookingDetail->where('booking_id', $id)->delete();

            return $booking->delete();
        });
    }

    /**
     * Cancel a booking by its ID.
     *
     * This method changes the status of a booking from PENDING to CANCELLED.
     * It retrieves the booking by its ID, ensuring that it is currently
     * in the PENDING status. If the booking is found, its status is updated
     * to CANCELLED and saved.
     *
     * @param int $id The ID of the booking to cancel.
     * @return bool True if the booking status was successfully updated, false otherwise.
     */
    public function cancelled($id): bool
    {
        $booking = $this->model->where('id', $id)
            ->whereIn('status', [
                BookingStatus::PENDING
            ])
            ->first();

        $booking->status = BookingStatus::CANCELLED;

        return $booking->save();
    }

    /**
     * Set a booking as NO_SHOW by its ID.
     *
     * This method updates a booking status from CONFIRMED to NO_SHOW.
     * It retrieves the booking by its ID, ensuring that it is currently
     * in the CONFIRMED status. If the booking is found, its status is updated
     * to NO_SHOW and saved.
     *
     * @param int $id The ID of the booking to set as no show.
     * @return bool True if the booking status was successfully updated, false otherwise.
     */
    public function noShow($id): bool
    {
        $booking = $this->model->where('id', $id)
            ->whereIn('status', [
                BookingStatus::CONFIRMED
            ])
            ->first();

        $booking->status = BookingStatus::NO_SHOW;

        return $booking->save();
    }

    /**
     * Confirm a booking by its ID.
     *
     * This method changes the status of a booking from PENDING to CONFIRMED.
     * It retrieves the booking by its ID, ensuring that it is currently
     * in the PENDING status. If the booking is found, its status is updated
     * to CONFIRMED and saved.
     *
     * @param  int  $id  The ID of the booking to confirm.
     * @return bool  True if the booking status was successfully updated, false otherwise.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  If the booking is not found.
     */

    public function confirm($id): bool
    {
        $booking = $this->model->where('status', BookingStatus::PENDING)->findOrFail($id);

        $booking->status = BookingStatus::CONFIRMED;

        return $booking->save();
    }

    /**
     * Check in a guest by the booking ID.
     *
     * This method retrieves a booking by its ID, given that the booking status
     * is CONFIRMED. If the booking status is not CONFIRMED, an exception is
     * thrown. It then updates the booking status to CHECK_IN and saves the
     * booking.
     *
     * @param  int  $id  The ID of the booking to be checked in.
     * @return bool  True if the booking status was successfully updated, false otherwise.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  If the booking is not found.
     */
    public function checkInGuest(int $idBooking, array $detailIds): bool
    {
        $booking = $this->model
            ->where('status', BookingStatus::CONFIRMED)
            ->findOrFail($idBooking);

        $this->bookingDetail
            ->whereIn('id', $detailIds)
            ->update([
                'checkin' => Carbon::now(),
                'status' => BookingDetailStatus::CHECK_IN,
            ]);

        $pendingRooms = $this->bookingDetail
            ->where('booking_id', $idBooking)
            ->where('status', '<>', BookingDetailStatus::CHECK_IN)
            ->exists();

        if (!$pendingRooms) {
            $booking->status = BookingStatus::CHECK_IN;
            $booking->check_in = Carbon::now();
            return $booking->save();
        }

        return true;
    }

    /**
     * Check out a booking and update its total payment and status.
     *
     * This method retrieves a booking by its ID, calculates the total service cost
     * from the invoice details table, and then updates the booking's total payment
     * and status. The new status is set to 'checked_out', and the total payment
     * is set to the calculated total service cost.
     *
     * @param  int  $bookingId  The ID of the booking to be checked out.
     * @param  array  $detailIds  The IDs of the booking details to be checked out.
     * @return \App\Models\Booking  The updated booking instance.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  If the booking is not found.
     * @throws \Exception
     */
    public function checkout(int $bookingId): Booking
    {
        return DB::transaction(function () use ($bookingId) {
            $booking = Booking::findOrFail($bookingId);

            if ($booking->status !== BookingStatus::CHECK_OUT) {
                throw new \Exception('Booking is not in CHECK_OUT status.');
            }

            $priceRoom = $booking->total_payment;

            $priceService = InvoiceDetail::where('booking_id', $bookingId)
                ->select(DB::raw('SUM(price * quantity) as total'))
                ->value('total');

            $totalPayment = $priceRoom + $priceService - $booking->deposit;

            $booking->update([
                'total_payment' => $totalPayment,
                'status' => BookingStatus::COMPLETED,
                'check_out' => Carbon::now(),
            ]);

            return $booking;
        });
    }

    public function checkOutRoom(int $bookingId, int $roomId): bool
    {
        return DB::transaction(function () use ($bookingId, $roomId) {
            $updatedRows = $this->bookingDetail
                ->where('booking_id', $bookingId)
                ->where('status', BookingDetailStatus::CHECK_IN)
                ->where('room_id', $roomId)
                ->update([
                    'status' => BookingDetailStatus::CHECK_OUT,
                    'checkout' => Carbon::now(),
                ]);

            if ($updatedRows > 0) {
                $remainingRooms = $this->bookingDetail
                    ->where('booking_id', $bookingId)
                    ->where('status', '!=', BookingDetailStatus::CHECK_OUT)
                    ->exists();

                if (!$remainingRooms) {
                    $booking = $this->model->find($bookingId);
                    if ($booking) {
                        $booking->status = BookingStatus::CHECK_OUT;
                        $booking->save();
                    }
                }
            }

            return $updatedRows > 0;
        });
    }
}
