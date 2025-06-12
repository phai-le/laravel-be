<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Arr;

class ServiceService
{

    public function __construct(
        protected Service $model,
    ) {}

    /**
     * Summary of get
     * @param array $data
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function get(array $data): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query  = $this->model->query();

        $query->when(Arr::get($data, 'name'), function ($q, $name) {
            $q->where('name', 'like', "%$name%");
        })
        ->when(Arr::get($data, 'price'), function ($q, $price) {
            $q->where('price', '=', $price);
        })
        ->when(Arr::get($data, 'status', '') !== '', function ($q) use ($data) {
            $q->where('status', '=', Arr::get($data, 'status'));
        });

        $perPage = $data['per_page'] ?? 10;
        $services = $query->paginate($perPage);

        return $services;
    }

    /**
     * Summary of show
     * @param int $id
     * @return Service
     */
    public function show(int $id): Service
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Create a service.
     *
     * @param  array  $data
     * @return \App\Models\Service
     */
    public function create(array $data): Service
    {
        $record = $this->model->create($data);

        return $record;
    }

    /**
     * Update a service.
     *
     * @param  array  $data
     * @param  int  $id
     * @return \App\Models\Service
     */
    public function update(array $data, $id): Service
    {
        $record = $this->model->findOrFail($id);
        $record->update($data);

        return $record;
    }

    /**
     * Delete a service.
     *
     * @param  int  $id
     * @return \App\Models\Service
     * @throws \Exception
     */
    public function delete($id): ?Service
    {
        $record = $this->model->findOrFail($id);

        $record->delete();

        return $record;
    }

    /**
     * Restore a service.
     *
     * @param  int  $id
     * @return \App\Models\Service
     */
    public function restore($id): ?Service
    {
        $service = $this->model->withTrashed()->findOrFail($id);

        if (!$service) {
            return null;
        }
        $service->restore();

        return $service;
    }
}
