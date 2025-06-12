<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthAdminService
{
    public function __construct(
        protected Admin $model,
    ) {}
    /**
     * Attempt to log in as an admin.
     *
     * @param  array  $data
     * @return \App\Models\Admin|false
     */
    public function login($data): bool
    {
        if (!Auth::guard('admin')->attempt($data)) {
            return false;
        }

        session()->regenerate();

        return true;
    }

    /**
     * Log out the admin user, invalidate the session, and regenerate the CSRF token.
     *
     * @return bool Returns true upon successful logout.
     */
    public function logout(): bool
    {
        Auth::guard('admin')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return true;
    }

    /**
     * Get the authenticated admin user.
     *
     * @return \App\Models\Admin
     */
    function get(): Admin
    {
        return Auth::guard('admin')->user();
    }


    /**
     * Update the authenticated admin user.
     *
     * @param  array  $data  The data containing new admin information.
     * @return \App\Models\Admin  The updated admin instance.
     */
    function update(array $data): Admin
    {
        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        $admin->update($data);

        return $admin;
    }

    /**
     * Change the password for the authenticated admin user.
     *
     * @param  array  $data  An array containing 'current_password' and 'new_password'.
     * @return bool  Returns true if the password change was successful, false otherwise.
     */
    function changePassword(array $data): bool
    {
        /** @var \App\Models\Admin $admin */
        $admin = Auth::guard('admin')->user();

        if (!$admin || !Hash::check($data['current_password'], $admin->password)) {
            return false;
        }

        $admin->password = Hash::make($data['new_password']);
        $admin->save();

        return true;
    }
}
