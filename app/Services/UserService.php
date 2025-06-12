<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
class UserService
{
    public function __construct(
        protected User $model,
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
        })
        ->when(Arr::get($data, 'status', '') !== '', function ($q) use ($data) {
            $q->where('status', '=', Arr::get($data, 'status'));    
        });

        $perPage = $data['per_page'] ?? 10;
        $users = $query->paginate($perPage);

        return $users;
    }

    /**
     * Summary of show
     * @param int $id
     * @return User
     */
    public function show(int $id): User
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Summary of store
     * @param array $data
     * @return User
     */
    public function store(array $data): User
    {
        return $this->model->create($data);
    }

    /**
     * Summary of update
     * @param int $id
     * @param array $data
     * @return User
     */
    public function update(int $id, array $data): User
    {
        $user = $this->model->findOrFail($id);
        $user->update($data);

        return $user;
    }

    /**
     * Summary of delete
     * @param int $id
     * @return User
     */
    public function delete(int $id): User
    {
        $user = $this->model->findOrFail($id);
        $user->delete();

        return $user;
    }

    /**
     * Summary of restore
     * @param int $id
     * @return User
     */
    public function restore(int $id): User
    {
        $user = $this->model->withTrashed()->findOrFail($id);
        $user->restore();

        return $user;
    }
}
