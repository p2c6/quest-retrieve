<?php

namespace App\Services\Entity\User;

use App\Http\Requests\Entity\User\StoreUserRequest;
use App\Http\Requests\Entity\User\UpdateUserRequest;
use App\Http\Resources\Entity\Role\RoleResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * List of all users.
     * 
     * @return App\Http\Resources\UserCollection
     */
    public function index(): UserCollection
    {
        return new UserCollection(User::paginate());
    }

    /**
     * Show a single user.
     * 
     * @param App\Models\User $user The model of the user which needs to be retrieved.
     * 
     * @return App\Http\Resources\UserResource
     */
    public function show(User $user): UserResource
    {
        return new UserResource(User::findOrFail($user->id));
    }
    
    /**
     * Handle user store request.
     * 
     * @param App\Http\Requests\Entity\User\StoreUserRequest $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function store(StoreUserRequest $request)
    {
        try {
            User::create([
                'email' => $request->email, 
                'password' => bcrypt($request->password), 
                'role_id' => $request->role_id
            ]);

            return response()->json(['message' => 'Successfully User Created.'], 201);

        } catch (ValidationException $validationException) {
            info("Validation Error on store user: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch(\Throwable $th) {
            info("Error on storing user: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during creating user.'
            ], 500);
        }
    }

    /**
     * Handle user update request.
     * 
     * @param App\Models\User $user The model of the user which needs to be updated.
     * @param App\Http\Requests\Entity\User\UpdateUserRequest $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function update(User $user, UpdateUserRequest $request)
    {
        try {
            $user->update([
                'email' => $request->email, 
                'password' => bcrypt($request->password), 
                'role_id' => $request->role_id
            ]);
            
            return response()->json(['message' => 'Successfully User Updated.'], 200);
            
        } catch (ValidationException $validationException) {
            info("Validation Error on update user: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch(\Throwable $th) {
            info("Error on updating user: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during updating user.'
            ], 500);
        }
    }

    /**
     * Handle delete user request.
     * 
     * @param App\Models\User $user The model of the user which needs to be deleted.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function delete(User $user): JsonResponse
    {
        try {
            if (Post::where('user_id', $user->id)->exists()) {
                return response()->json(['message' => 'Cannot delete user. There are posts associated with this user.'], 409);
            }
            
            $user->delete();
    
            return response()->json(['message' => 'Successfully User Deleted.'], 200);
        } catch (\Throwable $th) {
            info("Error on deleting role: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during deleting user.'
            ], 500);
        }
    }
}
