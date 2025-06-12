<?php

namespace App\Services;

use App\Enums\RoomStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Services\Utils\FileService;
use App\Models\Room;

class RoomService
{
    /**
     * Create a new RoomService instance.
     *
     * @param  \App\Models\Room  $model
     * @param  \App\Services\Utils\FileService  $fileService
     * @return void
     */
    public function __construct(
        protected Room $model,
        protected FileService $fileService,
    ) {}

    /**
     * Get rooms by search.
     *
     * @param array $search
     *            name: string
     *            room_type_id: int
     *            status: \App\Enums\RoomStatus
     *            per_page: int (default is 10)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function get(array $search): LengthAwarePaginator
    {
        $query = $this->model->when(isset($search['name']), function ($q) use ($search) {
            $q->where('name', 'like', '%' . $search['name'] . '%');
        })->when(isset($search['room_type_id']), function ($q) use ($search) {
            $q->where('room_type_id', 'like', '%' . $search['room_type_id'] . '%');
        })->when(isset($search['status']), function ($q) use ($search) {
            $q->where('status', $search['status']);
        });

        $perPage = $search['per_page'] ?? 12;
        $rooms = $query->paginate($perPage);

        return $rooms;
    }

    /**
     * Find a room by ID.
     *
     * @param  int  $id  The room ID.
     * @return \App\Models\Room
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findById(int $id): Room
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Insert a room.
     *
     * @param  array  $data  The form data.
     * @return \App\Models\Room
     */
    public function insert(array $data): Room
    {
        $thumbnailPath = $this->fileService->store($data['thumbnail'], 'room/thumbnails');

        $imagePaths = [];
        if (isset($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                $imagePaths[] = $this->fileService->store($image, 'room/images');
            }
        }

        return DB::transaction(function () use ($data, $thumbnailPath, $imagePaths) {
            $room = new Room;
            $room->name = $data['name'];
            $room->room_type_id = $data['room_type_id'];
            $room->description = $data['description'];
            $room->status = RoomStatus::ACTIVE;
            $room->thumbnail_path = $thumbnailPath;
            $room->save();

            foreach ($imagePaths as $imagePath) {
                $room->images()->create(['image_path' => $imagePath]);
            }

            return $room;
        });
    }

    /**
     * Update a room.
     *
     * @param  array  $data
     * @param  \App\Models\Room  $room
     * @return \App\Models\Room
     */
    public function update(array $data, Room $room): Room
    {
        if (isset($data['thumbnail'])) {
            $thumbnailPath = $this->fileService->store($data['thumbnail'], 'room/thumbnails');
            $this->fileService->delete($room->thumbnail_path);
            $room->thumbnail_path = $thumbnailPath;
        }

        $newImagePaths = [];
        if (!empty($data['images']) && is_array($data['images'])) {
            foreach ($data['images'] as $image) {
                $newImagePaths[] = $this->fileService->store($image, 'room/images');
            }
        }

        return DB::transaction(function () use ($room, $data, $newImagePaths) {
            $room->name = $data['name'];
            $room->room_type_id = $data['room_type_id'];
            $room->description = $data['description'];
            $room->status = $data['status'];

            $room->save();

            if (!empty($data['images_to_remove']) && is_array($data['images_to_remove'])) {
                $imagesToDelete = $room->images()->whereIn('id', $data['images_to_remove'])->get();

                foreach ($imagesToDelete as $image) {
                    $this->fileService->delete($image->image_path);
                    $image->delete();
                }
            }

            $room->images()->createMany(
                collect($newImagePaths)->map(fn($path) => ['image_path' => $path])->toArray()
            );

            return $room;
        });
    }

    /**
     * Delete a room.
     *
     * @param  int  $id
     * @return null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): bool
    {
        $room = Room::findOrFail($id);

        if ($room->bookingDetails()->exists()) {
            throw new \Exception('Room is exists Booking.');
        }

        return $room->delete();
    }

    /**
     * Restore a soft-deleted room.
     *
     * @param  int  $id  The ID of the room to restore.
     * @return \App\Models\Room  The restored room instance.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException  If no room with the given ID is found.
     */
    public function restore(int $id): Room
    {
        $room = Room::withTrashed()->findOrFail($id);
        $room->restore();

        return $room;
    }
}
