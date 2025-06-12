<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleAdmin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Property\CreatePropertyRequest;
use App\Http\Requests\Property\SearchPropertyRequest;
use App\Http\Requests\Property\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Services\PropertyService;
use Illuminate\Http\Response;

class PropertyController extends Controller
{
    public function __construct(
        protected PropertyService $service
    ) {
        $this->middleware('role:' . RoleAdmin::SUPERADMIN->value);
    }

    /**
     * Search properties.
     *
     * @param  \App\Http\Requests\Property\SearchPropertyRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(SearchPropertyRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $this->service->get($request->validated());

        return PropertyResource::collection($data);
    }

    /**
     * Create a new property.
     *
     * @param  \App\Http\Requests\Property\CreatePropertyRequest  $request
     * @return \App\Http\Resources\PropertyResource
     */
    public function store(CreatePropertyRequest $request): PropertyResource
    {
        $data = $this->service->create($request->validated());

        return new PropertyResource($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Property\UpdatePropertyRequest  $request
     * @param  int  $id
     * @return \App\Http\Resources\PropertyResource
     */
    public function update(UpdatePropertyRequest $request, int $id): PropertyResource
    {
        $data = $this->service->update($request->validated(), $id);

        return new PropertyResource($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): Response
    {
        $this->service->delete($id);

        return response()->noContent();
    }
}
