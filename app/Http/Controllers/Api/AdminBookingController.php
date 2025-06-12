<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CheckRoomAvaiableRequest;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Http\Requests\Booking\IdsRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\RoomResource;
use App\Services\BookingService;
use App\Http\Requests\Booking\CheckRoomUserAvaiableRequest;
use App\Http\Requests\Booking\SearchBookingRequest;
use App\Http\Requests\Booking\CheckOutRoomRequest;

class AdminBookingController extends Controller
{
    public function __construct(
        protected BookingService $service
    ) {}

    /**
     * Get all bookings.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection | Null
     */
    public function index(SearchBookingRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection | Null
    {
        $data = $this->service->get($request->validated());

        return BookingResource::collection($data);
    }

    /**
     * Get all bookings.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function indexCustomer(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $this->service->getCustomer();

        return BookingResource::collection($data);
    }

    /**
     * Create a new booking.
     *
     * @param  \App\Http\Requests\Booking\CreateBookingRequest  $request
     * @return \App\Http\Resources\BookingResource
     */
    public function store(CreateBookingRequest $request)
    {
        $data = $this->service->create($request->validated());

        return new BookingResource($data);
    }

    /**
     * Display the specified booking.
     *
     * @param  int  $id
     * @return \App\Http\Resources\BookingResource
     */
    public function show($id): BookingResource
    {
        $data = $this->service->findById($id);

        return new BookingResource($data);
    }

    /**
     * Check room availability for the given date range.
     *
     * This method retrieves all rooms that are available for booking
     * between the specified check-in and check-out dates. It excludes
     * rooms that are currently booked or occupied during this period.
     *
     * @param  \App\Http\Requests\Booking\CheckRoomAvaiableRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function checkRoomAvailability(CheckRoomAvaiableRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $this->service->checkRoom($request->validated());

        return RoomResource::collection($data);
    }

    /**
     * Update the specified booking in storage.
     *
     * @param  \App\Http\Requests\Booking\UpdateBookingRequest  $request
     * @param  int  $id
     * @return \App\Http\Resources\BookingResource
     */
    public function update(UpdateBookingRequest $request, $id): BookingResource
    {
        $data = $this->service->update($request->validated(), $id);

        return new BookingResource($data);
    }

    /**
     * Remove the specified booking from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): \Illuminate\Http\Response
    {
        $this->service->delete($id);

        return response()->noContent();
    }

    /**
     * Cancel the specified booking.
     *
     * This method sets the booking status to CANCELED and saves the booking.
     *
     * @param  int  $id  The ID of the booking to be canceled.
     * @return \Illuminate\Http\Response
     */
    public function cancelled($id): \Illuminate\Http\Response
    {
        $this->service->cancelled($id);

        return response()->noContent();
    }

    /**
     * Mark the specified booking as no-show.
     *
     * This method sets the booking status to NO_SHOW for the given booking ID
     * if the booking status is CONFIRMED. It updates the booking record
     * accordingly.
     *
     * @param  int  $id  The ID of the booking to be marked as no-show.
     * @return \Illuminate\Http\Response
     */
    public function noShow($id): \Illuminate\Http\Response
    {
        $this->service->noShow($id);

        return response()->noContent();
    }

    /**
     * Confirm the specified booking.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirm(int $id): \Illuminate\Http\Response
    {
        $this->service->confirm($id);

        return response()->noContent();
    }

    /**
     * Check in a guest with the specified booking ID.
     *
     * This method retrieves a booking by its ID, given that the booking status
     * is CONFIRMED. If the booking status is not CONFIRMED, an exception is
     * thrown. It then updates the booking status to CHECK_IN and saves the
     * booking.
     *
     * @param  int  $id  The ID of the booking to be checked in.
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  If the booking is not found.
     */
    public function checkInGuest(int $idBooking, IdsRequest $ids): \Illuminate\Http\Response
    {
        $this->service->checkInGuest($idBooking, $ids->validated()['ids']);

        return response()->noContent();
    }

    /**
     * Check out a guest with the specified booking ID.
     *
     * This method retrieves a booking by its ID, given that the booking status
     * is CHECK_IN. If the booking status is not CHECK_IN, an exception is
     * thrown. It then updates the booking status to CHECK_OUT and saves the
     * booking.
     *
     * @param  int  $id  The ID of the booking to be checked out.
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  If the booking is not found.
     */
    public function checkOutGuest(int $idBooking): \Illuminate\Http\Response
    {
        $this->service->checkout($idBooking);

        return response()->noContent();
    }

    public function checkOutRoom(int $idBooking, CheckOutRoomRequest $request): \Illuminate\Http\Response
    {
        $this->service->checkOutRoom($idBooking, $request->validated()['room_id']);

        return response()->noContent();
    }
}
