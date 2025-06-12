<?php

namespace App\Services;

use App\Models\Property;
use Illuminate\Support\Arr;

class PropertyService
{
    public function __construct(
        protected Property $model
    ) {}

    /**
     * Search properties by keyword
     *
     * @param  array  $keyword
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function get($payload)
    {
        $query  = $this->model->where('name', 'like', '%' . Arr::get($payload, 'keyword') . '%')->orderBy('id', 'desc');

        $per_page = Arr::get($payload, 'per_page', 10);

        return $query->get();
    }

    /**
     * Create a property.
     *
     * @param  array  $data
     * @return \App\Models\Property
     */
    public function create($data): Property
    {
        $record = $this->model->create($data);

        return $record;
    }

    /**
     * Update a property.
     *
     * @param  array  $data
     * @param  int  $id
     * @return \App\Models\Property
     */
    public function update($data, $id): Property
    {
        $record = $this->model->findOrFail($id);
        $record->update($data);
        return $record;
    }

    /**
     * Delete a property.
     *
     * @param  int  $id
     * @return \App\Models\Property
     * @throws \Exception
     */
    public function delete($id): bool
    {
        $record = $this->model->findOrFail($id);

        // check relationships before deleting
        if ($record->roomTypes()->exists()) {
            throw new \Exception("Property is exists Room Type.");
        }

        return $record->delete();
    }
}
