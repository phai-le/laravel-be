<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserHomeService;
use App\Http\Resources\HomeResource;

class UserHomeController extends Controller
{

    protected $userHomeService;

    public function __construct(UserHomeService $userHomeService)
    {
        $this->userHomeService = $userHomeService;
    }

    /**
     * Summary of index
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $roomTypes = $this->userHomeService->getRoomTypes();
        $roomTypeOffers = $this->userHomeService->getRoomTypesOffers();
        $roomTypeBetters = $this->userHomeService->getBetterRoomTypes();
        
        return response()->json([
            'roomTypes' => HomeResource::collection($roomTypes),
            'roomTypeOffers' => HomeResource::collection($roomTypeOffers),
            'roomTypeBetters' => HomeResource::collection($roomTypeBetters),
        ]);
    }
}
