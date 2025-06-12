<?php

namespace App\Services;

use App\Models\Room;
use App\Enums\RoomStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoomClientService
{
    /**
     * Construct a new RoomClientService instance.
     *
     * @param  \App\Models\Room  $model
     */
    public function __construct(
        protected Room $model,
    ) {}

    /**
     * Search rooms by filters.
     *
     * @param  array  $filters  The filters.
     *            room_type_id: int
     *            name: string
     *            per_page: int (default is 6)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function get(array $filters): LengthAwarePaginator
    {
        $rooms = $this->model->where('status', RoomStatus::ACTIVE)
            ->when(isset($filters['room_type_id']), function ($q) use ($filters) {
                $q->where('room_type_id', $filters['room_type_id']);
            })
            ->when(isset($filters['name']), function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['name'] . '%');
            });

        $perPage = $filters['per_page'] ?? 100;
        $rooms = $rooms->paginate($perPage);

        return $rooms;
    }

    /**
     * Find a room by its ID.
     *
     * @param  int  $id  The room ID.
     * @return \App\Models\Room
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function find($id)
    {
        $room = $this->model->where('status', RoomStatus::ACTIVE)->findOrFail($id);

        return $room;
    }
}
