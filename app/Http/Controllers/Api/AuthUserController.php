<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthUserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthUserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\AuthUserService  $service
     * @return void
     */
    public function __construct(
        protected AuthUserService $service
    ) {}

    public function login(LoginRequest $request)
    {
        $user = $this->service->login($request->validated());

        if (! $user) {
            return response()->json(['message' => 'Error'], 401);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return (new UserResource($user))->additional([
            'token' => $token
        ]);
    }

    public function register(RegisterRequest $request)
    {
        $user = $this->service->register($request->validated());

        if (isset($user['errors'])) {
            return response()->json(['errors' => $user['errors']], 422);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return (new UserResource($user))->additional([
            'token' => $token
        ]);
    }

    /**
     * Remove the user's access token from the database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successfully!'
        ]);
    }

    public function getProfile(): UserResource
    {
        $user = Auth::user();

        return new UserResource($user);
    }
}
