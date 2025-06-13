<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AdminService;
use App\Http\Resources\AdminResource;
use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\UpdateAdminRequest;

class AdminController extends Controller
{
    protected AdminService $AdminService;

    /**
     * Summary of __construct
     * @param \App\Services\AdminService $AdminService
     */
    public function __construct(AdminService $AdminService)
    {
        $this->AdminService = $AdminService;
        $this->middleware('role:' . RoleAdmin::SUPERADMIN->value);
    }

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $request->query();
        $Admins = $this->AdminService->get($data);

        return AdminResource::collection($Admins);
    }

    /**
     * Summary of show
     * @param int $id
     * @return AdminResource
     */
    public function show(int $id): AdminResource
    {
        $Admin = $this->AdminService->show($id);

        return new AdminResource($Admin);
    }

    /**
     * Summary of store
     * @param CreateAdminRequest $request
     * @return AdminResource
     */
    public function store(CreateAdminRequest $request): AdminResource
    {
        $data = $request->validated();
        $Admin = $this->AdminService->store($data);

        return new AdminResource($Admin);
    }

    /**
     * Summary of update
     * @param UpdateAdminRequest $request
     * @param int $id
     * @return AdminResource
     */
    public function update(UpdateAdminRequest $request, int $id): AdminResource
    {
        $data = $request->validated();
        $Admin = $this->AdminService->update($id, $data);

        return new AdminResource($Admin);
    }

    /**
     * Summary of destroy
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): \Illuminate\Http\Response
    {
        $this->AdminService->delete($id);

        return response()->noContent();
    }

    /**
     * Summary of restore
     * @param int $id
     * @return AdminResource
     */
    public function restore(int $id): AdminResource
    {
        $Admin = $this->AdminService->restore($id);

        return new AdminResource($Admin);
    }
}
