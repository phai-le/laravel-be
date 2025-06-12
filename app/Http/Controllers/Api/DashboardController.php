<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\RevenueRequest;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $service
    ) {}

    /**
     * Get revenue data for a given type (day, week, month).
     *
     * @param \App\Http\Requests\Dashboard\RevenueRequest $request
     * @return array
     */
    public function revenue(RevenueRequest $request)
    {
        $data = $this->service->getRevenue($request->validated()['type']);

        return $data;
    }

    /**
     * Get all rooms that are checking out today.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTodayCheckoutRooms()
    {
        return $this->service->getTodayCheckoutRooms();
    }

    /**
     * Count the number of unread contacts.
     *
     * @return int
     */
    public function countContacts(): int
    {
        return $this->service->countContacts();
    }

    /**
     * Get the number of users in the system.
     *
     * @return int
     */
    public function countUsers(): int
    {
        return $this->service->countUsers();
    }

    /**
     * Count the number of bookings with a pending status.
     *
     * @return int
     */
    public function countBookings(): int
    {
        return $this->service->countBookings();
    }
}
