<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthAdminService;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class AuthAdminController extends Controller
{
    public function __construct(
        protected AuthAdminService $service
    ) {}

    /**
     * Authenticate a user and return the user data with an access token.
     *
     * @param \Modules\ApiUserManager\Http\Controllers\Requests\Auth\LoginRequest $request
     *
     */
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

    /**
     * Logout and delete the current access token.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successfully!'
        ]);
    }
}
