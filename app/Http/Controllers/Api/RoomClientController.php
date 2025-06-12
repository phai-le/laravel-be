<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomClientResource;
use Illuminate\Http\Request;
use App\Services\RoomClientService;
use App\Http\Resources\RoomDetailClientResource;

class RoomClientController extends Controller
{
    public function __construct(
        protected RoomClientService $service,
    ) {}

    public function index(Request $request)
    {
        $rooms = $this->service->get($request->all());

        return RoomClientResource::collection($rooms);
    }

    public function show($id)
    {
        $room = $this->service->find($id);
        return new RoomDetailClientResource($room);
    }
}
