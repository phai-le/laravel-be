<?php

namespace App\Services;

use App\Models\Admin;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function __construct(
        protected Admin $model,
    ) {}

    /**
     * Summary of get
     * @param array $data
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function get(array $data): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = $this->model->query();

        $query->when(Arr::get($data, 'name'), function ($q, $name) {
            $q->where('name', 'like', "%$name%");
        })
            ->when(Arr::get($data, 'email'), function ($q, $email) {
                $q->where('email', 'like', "%$email%");
            })
            ->when(Arr::get($data, 'phone'), function ($q, $phone) {
                $q->where('phone', 'like', "%$phone%");
            });

        $perPage = $data['per_page'] ?? 10;
        $Admins = $query->paginate($perPage);

        return $Admins;
    }

    /**
     * Summary of show
     * @param int $id
     * @return Admin
     */
    public function show(int $id): Admin
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Summary of store
     * @param array $data
     * @return Admin
     */
    public function store(array $data): Admin
    {
        return $this->model->create(
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'password' => Hash::make($data['password']),
                'role' => 'staff'
            ]
        );
    }

    /**
     * Summary of update
     * @param int $id
     * @param array $data
     * @return Admin
     */
    public function update(int $id, array $data): Admin
    {
        $Admin = $this->model->findOrFail($id);
        $Admin->update($data);

        return $Admin;
    }

    /**
     * Summary of delete
     * @param int $id
     * @return Admin
     */
    public function delete(int $id): Admin
    {
        $Admin = $this->model->findOrFail($id);
        $Admin->delete();

        return $Admin;
    }

    /**
     * Summary of restore
     * @param int $id
     * @return Admin
     */
    public function restore(int $id): Admin
    {
        $Admin = $this->model->withTrashed()->findOrFail($id);
        $Admin->restore();

        return $Admin;
    }
}
