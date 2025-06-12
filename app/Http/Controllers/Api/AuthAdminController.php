<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthAdminService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\UpdateProfileRequest;

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
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->service->login($request->validated());

        if (! $user) {
            return response()->json('Invalid credentials')->setStatusCode(401);
        }

        return response()->json('Logged in successfully')->setStatusCode(200);
    }

    /**
     * Logout and delete the current access token.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): Response
    {
        $this->service->logout();

        return response()->noContent();
    }

    /**
     * Get the profile of the current admin user.
     *
     * @return \App\Http\Resources\UserResource
     */
    public function getProfile(): UserResource
    {
        $user = $this->service->get();

        return new UserResource($user);
    }

    /**
     * Update the authenticated admin user's information.
     *
     * @param \App\Http\Requests\Auth\UpdateProfileRequest $request The request containing the new admin information.
     * @return \App\Http\Resources\UserResource The updated admin user instance.
     */
    public function updateProfile(UpdateProfileRequest $request): UserResource
    {
        $user = $this->service->update($request->validated());

        return new UserResource($user);
    }

    /**
     * Change the password for the authenticated admin user.
     *
     * @param \App\Http\Requests\Auth\ChangePasswordRequest $request The request containing the current and new passwords.
     * @return \Illuminate\Http\JsonResponse Returns a 200 response with a success message on success or a 400 response with an error message on failure.
     */
    public function changePassword(ChangePasswordRequest $request): jsonResponse
    {
        $succsess = $this->service->changePassword($request->validated());

        if (!$succsess) {
            return response()->json('Current password is incorrect')->setStatusCode(400);
        }

        return response()->json('Password changed successfully')->setStatusCode(200);
    }
}
