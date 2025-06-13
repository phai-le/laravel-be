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

    public function register($data)
    {
        $user = $this->model->create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

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
