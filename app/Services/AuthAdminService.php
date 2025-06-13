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
    public function login($data)
    {
        $user = $this->model
            ->where('email', $data['email'])
            ->first();

        if (! $user) {
            return false;
        }

        if (! $user && Hash::check($data['password'], $user->password)) {
            return false;
        }

        return $user;
    }

}
