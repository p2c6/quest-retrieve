<?php

namespace App\Services\Entity\Role;

use App\Http\Resources\Entity\Role\RoleCollection;
use App\Http\Resources\Entity\Role\RoleResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\QueryBuilder;

class RoleService
{
    /**
     * List of all roles.
     * 
     * @param App\Models\Role $role The model of the role which needs to be retrieved.
     * @return Illuminate\Http\JsonResponse
     */
    public function index($request): JsonResponse
    {
        $roles = QueryBuilder::for(Role::class)
        ->allowedSorts($request->query('sort'))
        ->allowedFilters('name')
        ->paginate(5)
        ->appends($request->query('keyword'));

        return response()->json($roles);
    }

    /**
     * List of all roles for dropdown.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function dropdownRoles(): JsonResponse
    {
        $roles = Role::get(['id', 'name']);

        return response()->json($roles);
    }

    /**
     * Show a single role.
     * 
     * @param App\Models\Role $role The model of the role which needs to be retrieved.
     * @return App\Http\Resources\Entity\Role\RoleResource
     */
    public function show(Role $role): RoleResource
    {
        return new RoleResource(Role::findOrFail($role->id));
    }
    
    /**
     * Handle role store request.
     * 
     * @param App\Http\Requests\Authentication\LoginRequest $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function store($request)
    {
        try {
            Role::create(['name' => $request->name]);

            return response()->json(['message' => 'Successfully Role Created.'], 201);

        } catch (ValidationException $validationException) {
            info("Validation Error on store role: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch(\Throwable $th) {
            info("Error on storing role role: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during creating role.'
            ], 500);
        }
    }

    /**
     * Handle role update request.
     * 
     * @param App\Models\Role $role The model of the role which needs to be updated.
     * @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function update($role, $request)
    {
        try {
            $role->update(['name' => $request->name]);

            return response()->json(['message' => 'Successfully Role Updated.'], 200);
            
        } catch (ValidationException $validationException) {
            info("Validation Error on update role: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch(\Throwable $th) {
            info("Error on storing role role: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during updating role.'
            ], 500);
        }
    }

    /**
     * Handle delete role request.
     * 
     * @param App\Models\Role $role The model of the role which needs to be deleted.
     * @return Illuminate\Http\JsonResponse
     */
    public function delete(Role $role): JsonResponse
    {
        try {
            if (User::where('role_id', $role->id)->exists()) {
                return response()->json(['message' => 'Cannot delete role. There are users associated with this role.'], 409);
            }
            
            $role->delete();
    
            return response()->json(['message' => 'Successfully Role Deleted.'], 200);
        } catch (\Throwable $th) {
            info("Error on deleting role: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during deleting role.'
            ], 500);
        }
    }
}
