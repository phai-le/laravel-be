<?php

namespace App\Services;

use App\Models\Property;
use App\Models\RoomType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class RoomTypeService
{
    public function __construct(
        protected RoomType $model,
        protected Property $property
    ) {}

    /**
     * Search room types by keyword.
     *
     * @param  array  $keyword
     * @return \Illuminate\Support\Collection
     */
    public function get($keyword): \Illuminate\Support\Collection
    {
        $query  = $this->model->query();
        $query->where('name', 'like', '%' . Arr::get($keyword, 'keyword') . '%');

        return $query->get();
    }

    /**
     * Create a room type.
     *
     * @param  array  $data
     * @return \App\Models\RoomType
     */
    public function create($data): RoomType
    {
        return DB::transaction(function () use ($data) {
            $roomType = [
                'name' => $data['name'],
                'price' => $data['price'],
            ];

            $record = $this->model->create($roomType);

            $record->roomProperties()->createMany($data['properties']);

            return $record;
        });
    }

    /**
     * Update a room type.
     *
     * @param  array  $data
     * @param  int  $id
     * @return \App\Models\RoomType
     */
    public function update($data, $id): RoomType
    {
        return DB::transaction(function () use ($data, $id) {
            $record = $this->model->findOrFail($id);
            $roomType = [
                'name' => $data['name'],
                'price' => $data['price'],
            ];

            $record->update($roomType);

            $record->roomProperties()->delete();
            $record->roomProperties()->createMany($data['properties']);

            return $record;
        });
    }

    /**
     * Delete a room type.
     *
     * @param  int  $id
     * @return bool|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete($id)
    {
        $record = $this->model->findOrFail($id);

        if ($record->rooms()->exists()) {
            throw new \Exception('Room Type is exists Room.');
        }

        return $record->delete();
    }
}
