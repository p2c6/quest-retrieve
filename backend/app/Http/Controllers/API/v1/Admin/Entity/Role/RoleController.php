<?php

namespace App\Http\Controllers\API\v1\Admin\Entity\Role;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\Role\StoreRoleRequest;
use App\Http\Requests\Entity\Role\UpdateRoleRequest;
use App\Http\Resources\Entity\Role\RoleCollection;
use App\Http\Resources\Entity\Role\RoleResource;
use App\Models\Role;
use App\Services\Entity\Role\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * The RoleService instance.
     * 
     * @var RoleService
     */
    private $service;

    /**
     * The RoleController constructor.
     * 
     * @param RoleService $service The instance of RoleService
     */
    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    /**
     * List of all roles.
     * 
     * @param App\Models\Role $role The model of the role which needs to be retrieved.
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->service->index($request->query('keyword'));
    }

    /**
     * Show a single role.
     * 
     * @param App\Models\Role $role The model of the role which needs to be retrieved.
     * @return App\Http\Resources\Entity\Role\RoleResource
     */
    public function show(Role $role): RoleResource
    {
        return $this->service->show($role);
    }

    /**
     * Handle store role request.
     * 
     * @param App\Http\Requests\Entity\Role\StoreRoleRequest $request The HTTP request object containing role data.
     * @return Illuminate\Http\JsonResponse
     */
    public function store(StoreRoleRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }

    /**
     * Handle update role request.
     * 
     * @param App\Models\Role $role The model of the role which needs to be updated.
     * @param \Illuminate\Http\Request $request The HTTP request object containing role data.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Role $role, UpdateRoleRequest $request): JsonResponse
    {
        return $this->service->update($role, $request);
    }

    /**
     * Handle delete role.
     * 
     * @param App\Models\Role $role The model of the role which needs to be deleted.
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        return $this->service->delete($role);
    }

    /**
     * List of all roles for dropdown.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function dropdownRoles(): JsonResponse
    {
        return $this->service->dropdownRoles();
    }
}
