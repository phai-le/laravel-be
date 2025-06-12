<?php

namespace App\Services;

use App\Enums\BookingDetailStatus;
use App\Enums\BookingStatus;
use App\Enums\ContactEnum;
use App\Models\Booking;
use App\Models\BookingDetail;
use App\Models\Contact;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(
        protected Booking $model
    ) {}

    /**
     * Get revenue data for a given time period.
     *
     * @param string $type The type of revenue data to get. Valid values are 'day', 'week', or 'month'.
     * @return \Illuminate\Support\Collection
     *
     * @throws \InvalidArgumentException If the type is invalid.
     */
    public function getRevenue($type)
    {
        switch ($type) {
            case 'day':
                return DB::table('bookings')
                    ->selectRaw('DATE(created_at) AS revenue_date, SUM(total_payment) AS revenue')
                    ->where('created_at', '>=', now()->subDays(6)->startOfDay())
                    ->groupBy('revenue_date')
                    ->orderBy('revenue_date', 'ASC')
                    ->get();

            case 'week':
                return DB::table('bookings')
                    ->selectRaw('YEARWEEK(created_at, 1) AS revenue_week, SUM(total_payment) AS revenue')
                    ->where('created_at', '>=', now()->subWeeks(8)->startOfDay())
                    ->groupBy('revenue_week')
                    ->orderBy('revenue_week', 'ASC')
                    ->get();

            case 'month':
                return DB::table('bookings')
                    ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') AS revenue_month, SUM(total_payment) AS revenue")
                    ->whereYear('created_at', now()->year)
                    ->groupBy('revenue_month')
                    ->orderBy('revenue_month', 'ASC')
                    ->get();

            default:
                throw new \InvalidArgumentException('Invalid revenue type');
        }
    }

    /**
     * Retrieve a collection of rooms with checkouts scheduled for today.
     *
     * This function queries the BookingDetail model to find all booking details
     * where the checkout date is today. It returns an array of room and guest details
     * including booking ID, room ID, room name, check-in and check-out times, booking status,
     * guest name, and guest phone number, sorted by check-in time.
     *
     * @return \Illuminate\Support\Collection A collection of today's checkout room details.
     */

    function getTodayCheckoutRooms()
    {
        $today = Carbon::today()->toDateString();

        $todayBookings = BookingDetail::with(['room', 'booking'])
            ->where(function ($query) use ($today) {
                $query->whereDate('checkin_at', $today)
                    ->orWhereDate('checkout_at', $today);
            })
            ->orderBy('checkin_at', 'ASC')
            ->get();

        $results = $todayBookings->map(function ($bookingDetail) {
            return [
                'booking_id' => $bookingDetail->id,
                'room_id' => $bookingDetail->room_id,
                'room_name' => $bookingDetail->room ? $bookingDetail->room->name : 'N/A',
                'checkin_at' => $bookingDetail->checkin_at,
                'checkout_at' => $bookingDetail->checkout_at,
                'status' => $bookingDetail->status,
                'guest_name' => $bookingDetail->booking->guest_name,
                'guest_phone' => $bookingDetail->booking->guest_phone,
            ];
        });

        return $results;
    }

    /**
     * Get the number of contacts that have not received a response.
     *
     * @return int The number of unresponded contacts.
     */
    public function countContacts()
    {
        return Contact::where('status', ContactEnum::Unresponse)->count();
    }

    /**
     * Get the count of active users.
     *
     * This method queries the User model to count the number of users
     * with a status of active (status = 1).
     *
     * @return int The number of active users.
     */

    public function countUsers()
    {
        return User::where('status', 1)->count();
    }

    /**
     * Count the number of bookings with a pending status.
     *
     * @return int
     */
    public function countBookings()
    {
        return Booking::where('status', BookingStatus::PENDING)->count();
    }
}
