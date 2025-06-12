<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CheckRoomAvaiableRequest;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Http\Requests\Booking\IdsRequest;
use App\Http\Requests\Booking\UpdateBookingClientRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\RoomResource;
use App\Services\BookingService;
use App\Http\Requests\Booking\CheckRoomUserAvaiableRequest;
use App\Http\Requests\Booking\SearchBookingRequest;
use App\Http\Requests\Booking\CheckOutRoomRequest;

class UserBookingController extends Controller
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
     * Summary of update
     * @param \App\Http\Requests\Booking\UpdateBookingRequest $request
     * @param mixed $id
     * @return BookingResource
     */
    public function update(UpdateBookingClientRequest $request, $id): BookingResource
    {
        $data = $this->service->updateClient($request->validated(), $id);

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
}
