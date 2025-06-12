<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleAdmin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Room\SearchRoomRequest;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Http\Resources\RoomDetailResource;
use App\Http\Resources\RoomResource;
use App\Services\RoomService;

class RoomController extends Controller
{
    /**
     * Create a new RoomController instance.
     *
     * @param \App\Services\RoomService $roomService
     */
    public function __construct(
        protected RoomService $roomService
    ) {
        $this->middleware('role:' . RoleAdmin::SUPERADMIN->value);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SearchRoomRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $rooms = $this->roomService->get($request->validated());

        return RoomResource::collection($rooms);
    }

    /**
     * Show the form for store a new resource.
     */
    public function store(StoreRoomRequest $request): RoomResource
    {
        $room = $this->roomService->insert($request->validated());

        return new RoomResource($room);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): RoomDetailResource
    {
        $room = $this->roomService->findById($id);

        return new RoomDetailResource($room);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoomRequest $request, int $id): RoomResource
    {
        $room = $this->roomService->findById($id);
        $roomAfterUpdate = $this->roomService->update($request->validated(), $room);

        return new RoomResource($roomAfterUpdate);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $this->roomService->delete($id);

        return response()->noContent();
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(int $id): RoomResource
    {
        $room = $this->roomService->restore($id);

        return new RoomResource($room);
    }
}
