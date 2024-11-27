<?php

namespace App\Http\Controllers\API\v1\Admin\Entity\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\Role\UpdateRoleRequest;
use App\Http\Requests\Entity\User\StoreUserRequest;
use App\Http\Requests\Entity\User\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Services\Entity\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    /**
     * The UserService instance.
     * 
     * @var UserService
     */
    private $service;

    /**
     * The UserController constructor.
     * 
     * @param UserService $service The instance of UserService
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * List of all roles.
     * 
     * @param App\Models\User $user The model of the user which needs to be retrieved.
     * 
     * @return App\Http\Resources\UserCollection|Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse|UserCollection
    {
        if(Gate::denies('viewAny', User::class)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->index();
    }

    /**
     * Show a single user.
     * 
     * @param App\Models\User $user The model of the user which needs to be retrieved.
     * 
     * @return App\Http\Resources\UserResource|Illuminate\Http\JsonResponse
     */
    public function show(User $user): UserResource|JsonResponse
    {
        if(Gate::denies('view', $user)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->show($user);
    }

    /**
     * Handle store role request.
     * 
     * @param App\Http\Requests\Entity\User\StoreUserRequest $request The HTTP request object containing role data.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        if(Gate::denies('create', User::class)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->store($request);
    }

    /**
     * Handle update user request.
     * 
     * @param App\Models\User $user The model of the user which needs to be updated.
     * @param \Illuminate\Http\Request $request The HTTP request object containing role data.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function update(User $user, UpdateUserRequest $request): JsonResponse
    {
        if(Gate::denies('update', $user)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->update($user, $request);
    }

    /**
     * Handle delete role.
     * 
     * @param App\Models\User $user The model of the role which needs to be deleted.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
        return $this->service->delete($user);
    }
}
