<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        protected AuthUserService $authUserService
    ) {}

    /**
     * Register a new user.
     *
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $this->authUserService->register($request->validated());

        return response()->json('Registered successfully!')->setStatusCode(201);
    }

    /**
     * Authenticate a user with the given credentials.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request The request containing user credentials.
     * @return \Illuminate\Http\JsonResponse Returns a 401 response with an error message on failure or a no-content response on success.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authUserService->login($request->validated());

        if (Auth::user()->status === 2) {
            $this->authUserService->logout();

            return response()->json('Account is locked')->setStatusCode(423);
        }

        if (!$result) {
            return response()->json('Invalid credentials')->setStatusCode(401);
        }

        return response()->json('Logged in successfully')->setStatusCode(200);
    }

    /**
     * Log the user out by deleting the current access token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(): Response
    {
        $this->authUserService->logout();

        return response()->noContent();
    }

    /**
     * Get the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProfile(): UserResource
    {
        $user = $this->authUserService->getUser();

        return new UserResource($user);
    }

    /**
     * Change the password for the authenticated user.
     *
     * @param \App\Http\Requests\Auth\ChangePasswordRequest $request The request containing the current and new passwords.
     * @return \Illuminate\Http\JsonResponse Returns a 200 response with a success message on success or a 400 response with an error message on failure.
     */
    public function changePassword(ChangePasswordRequest $request): jsonResponse
    {
        $succsess = $this->authUserService->changePassword($request->validated());

        if (!$succsess) {
            return response()->json('Current password is incorrect')->setStatusCode(400);
        }

        return response()->json('Password changed successfully')->setStatusCode(200);
    }

    /**
     * Update the authenticated user's information.
     *
     * @param  array  $data  The data containing new user information.
     * @return \App\Http\Resources\UserResource  The updated user instance.
     */
    public function updateProfile(UpdateProfileRequest $request): UserResource
    {
        $user = $this->authUserService->update($request->validated());

        return new UserResource($user);
    }
}
