<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthUserService
{
    /**
     * Construct a new AuthUserService instance.
     *
     * @param  \App\Models\User  $model
     */
    public function __construct(
        protected User $model,
    ) {}

    /**
     * Registers a new user and returns the User instance.
     *
     * @param  array  $data
     * @return array  Returns an array containing the status, message, and code.
     */
    public function register(array $data): bool
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        session()->regenerate();

        return true;
    }

    /**
     * Authenticate a user with the given credentials.
     *
     * @param  array  $credentials  The credentials for authentication.
     * @return array  Returns an array containing the status, message, and code.
     */
    public function login(array $credentials): bool
    {
        if (!Auth::attempt($credentials)) {
            return false;
        }

        session()->regenerate();

        return true;
    }

    /**
     * Log the user out and invalidate the session.
     *
     * @return array  Returns an array containing the status and message.
     */
    public function logout(): bool
    {
        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return true;
    }

    /**
     * Get the authenticated user.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return Auth::user();
    }

    /**
     * Change the password for the authenticated user.
     */
    public function changePassword(array $data): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !Hash::check($data['current_password'], $user->password)) {
            return false;
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        return true;
    }

    /**
     * Update the authenticated user's information.
     *
     * @param  array  $data  The data containing new user information.
     * @return \App\Models\User  The updated user instance.
     */
    public function update(array $data): User
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->update($data);

        return $user;
    }
}
