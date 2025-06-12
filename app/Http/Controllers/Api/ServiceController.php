<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleAdmin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Service\SearchServiceRequest;
use App\Http\Requests\Service\CreateServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Services\ServiceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ServiceController extends Controller
{
    public function __construct(
        protected ServiceService $service,
    ) {
        $this->service = $service;
        $this->middleware('role:' . RoleAdmin::SUPERADMIN->value)->except('index', 'show');
    }

    /**
     * Search services.
     *
     * @param  \App\Http\Requests\Service\SearchServiceRequest  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(SearchServiceRequest $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $request->query();

        $services = $this->service->get($data);

        return ServiceResource::collection($services);
    }

    /**
     * Summary of show
     * @param int $id
     * @return ServiceResource
     */
    public function show(int $id): ServiceResource
    {
        $data = $this->service->show($id);

        return new ServiceResource($data);
    }

    /**
     * Create a service.
     *
     * @param  \App\Http\Requests\Service\CreateServiceRequest  $request
     * @return \App\Http\Resources\ServiceResource
     */
    public function store(CreateServiceRequest $request): ServiceResource
    {
        $data = $request->validated();

        $service = $this->service->create($data);

        return new ServiceResource($service);
    }

    /**
     * Update a service.
     *
     * @param  \App\Http\Requests\Service\UpdateServiceRequest  $request
     * @param  int  $id
     * @return \App\Http\Resources\ServiceResource
     */
    public function update(UpdateServiceRequest $request, int $id): ServiceResource
    {
        $data = $this->service->update($request->validated(), $id);

        return new ServiceResource($data);
    }

    /**
     * Delete a service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): Response
    {
        $this->service->delete($id);

        return response()->noContent();
    }

    /**
     * Restore a service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore($id): Response
    {
        $service = $this->service->restore($id);

        return response()->noContent();
    }
}
