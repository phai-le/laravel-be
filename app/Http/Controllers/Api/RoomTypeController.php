<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleAdmin;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoomType\CreateRoomTypeRequest;
use App\Http\Requests\RoomType\SearchRoomTypeRequest;
use App\Http\Requests\RoomType\UpdateRoomTypeRequest;
use App\Http\Resources\RoomTypeResource;
use App\Services\RoomTypeService;

class RoomTypeController extends Controller
{
    public function __construct(
        protected RoomTypeService $service
    ) {
        $this->middleware('role:' . RoleAdmin::SUPERADMIN->value);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(SearchRoomTypeRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $this->service->get($request->validated());

        return RoomTypeResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRoomTypeRequest $request): RoomTypeResource
    {
        $data = $this->service->create($request->validated());

        return new RoomTypeResource($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\RoomType\UpdateRoomTypeRequest  $request
     * @param  int  $id
     * @return \App\Http\Resources\RoomTypeResource
     */
    public function update(UpdateRoomTypeRequest $request, int $id): RoomTypeResource
    {
        $data = $this->service->update($request->validated(), $id);

        return new RoomTypeResource($data);
    }

    /**
     * Remove the specified room type from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * @throws \Exception if the room type is associated with existing rooms.
     */
    public function destroy(int $id): \Illuminate\Http\Response
    {
        $this->service->delete($id);

        return response()->noContent();
    }
}
