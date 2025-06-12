<?php

namespace App\Services;

use App\Models\RoomType;
use App\Models\Booking;
use App\Enums\BookingStatus;

class UserHomeService
{
    public function __construct(
        protected RoomType $model,
    ) {}

    /**
     * Summary of getRoomTypes
     * @return \Illuminate\Database\Eloquent\Collection<int, RoomType>
     */

    public function getRoomTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model::all();
    }
    public function getRoomTypesOffers(): \Illuminate\Database\Eloquent\Collection
    {
        $roomTypeOffers = $this->model->select('room_types.*')
            ->with(['roomPropertiesLimited', 'rooms' => function($query) {
                $query->select('rooms.id', 'rooms.room_type_id', 'rooms.thumbnail_path');
            }])
            ->limit(3)
            ->get();

        return $roomTypeOffers;
    }

    public function getBetterRoomTypes(): \Illuminate\Database\Eloquent\Collection
    {
        $roomTypeBetters = $this->model->select('room_types.*')
            ->join('rooms', 'room_types.id', '=', 'rooms.room_type_id')
            ->join('booking_details', 'rooms.id', '=', 'booking_details.room_id')
            ->groupBy('room_types.id')
            ->orderByRaw('COUNT(booking_details.id) DESC')
            ->with(['properties', 'rooms' => function($query) {
                $query->select('rooms.id', 'rooms.room_type_id', 'rooms.thumbnail_path');
            }])
            ->limit(4)
            ->get();

        if ($roomTypeBetters->isEmpty() || $roomTypeBetters->count() < 4) {
            $roomTypeBetters = $this->model->inRandomOrder()
                ->with(['properties', 'rooms' => function($query) {
                    $query->select('rooms.id', 'rooms.room_type_id', 'rooms.thumbnail_path');
                }])
                ->limit(4)
                ->get();
        }

        return $roomTypeBetters;
    }
}
