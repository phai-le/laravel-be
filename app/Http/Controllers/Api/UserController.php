<?php

namespace App\Http\Controllers\Api;

use App\Enums\RoleAdmin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Resources\UserResource;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;

class UserController extends Controller
{
    protected UserService $userService;

    /**
     * Summary of __construct
     * @param \App\Services\UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('role:' . RoleAdmin::SUPERADMIN->value)->except('index', 'show', 'store', 'update');
    }

    /**
     * Summary of index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $data = $request->query();
        $users = $this->userService->get($data);

        return UserResource::collection($users);
    }

    /**
     * Summary of show
     * @param int $id
     * @return UserResource
     */
    public function show(int $id): UserResource
    {
        $user = $this->userService->show($id);

        return new UserResource($user);
    }

    /**
     * Summary of store
     * @param CreateUserRequest $request
     * @return UserResource
     */
    public function store(CreateUserRequest $request): UserResource
    {
        $data = $request->validated();
        $user = $this->userService->store($data);

        return new UserResource($user);
    }

    /**
     * Summary of update
     * @param UpdateUserRequest $request
     * @param int $id
     * @return UserResource
     */
    public function update(UpdateUserRequest $request, int $id): UserResource
    {
        $data = $request->validated();
        $user = $this->userService->update($id, $data);

        return new UserResource($user);
    }

    /**
     * Summary of destroy
     * @param int $id
     * @return UserResource
     */
    public function destroy(int $id): UserResource
    {
        $user = $this->userService->delete($id);

        return new UserResource($user);
    }

    /**
     * Summary of restore
     * @param int $id
     * @return UserResource
     */
    public function restore(int $id): UserResource
    {
        $user = $this->userService->restore($id);

        return new UserResource($user);
    }
}
