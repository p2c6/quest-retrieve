<?php

namespace App\Services\Entity\User;

use App\Filters\FilterUser;
use App\Http\Requests\Entity\User\StoreUserRequest;
use App\Http\Requests\Entity\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Models\User;
use App\Services\UserProfile\UserProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserService
{
    /**
     * UserService contructor.
     * 
     * @param UserProfileService $userProfileService The instance of UserProfileService.
     */
    public function __construct(protected UserProfileService $userProfileService)
    {
        
    }

    /**
     * List of all users.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function index($keyword): JsonResponse
    {
        $users = QueryBuilder::for(User::class)
        ->select('id', 'email')
        ->with(['profile' => function($q) {
            $q->select(
                'user_id', 
                'last_name', 
                'first_name', 
                DB::raw('DATE_FORMAT(created_at, "%b %d, %Y") as birthday'),
                'contact_no'
            );
        }])
        ->allowedFilters([AllowedFilter::custom('keyword', new FilterUser)])
        ->paginate(5)
        ->appends($keyword);
                
        return response()->json($users);
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
            DB::beginTransaction();

            $user = User::create([
                'email' => $request->email, 
                'email_verified_at' => now(),
                'password' => bcrypt($request->password),
                'role_id' => $request->role_id
            ]);

            $this->userProfileService->storeUserProfile($user->id, $request);

            DB::commit();

            return response()->json(['message' => 'Successfully User Created.'], 201);

        } catch (ValidationException $validationException) {
            info("Validation Error on store user: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch(\Throwable $th) {
            DB::rollBack();
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
            DB::beginTransaction();

            $user->update([
                'email' => $request->email, 
                'password' => bcrypt($request->password), 
                'role_id' => $request->role_id
            ]);

            $this->userProfileService->update($user, $request);

            DB::commit();
            
            return response()->json(['message' => 'Successfully User Updated.'], 200);
            
        } catch (ValidationException $validationException) {
            info("Validation Error on update user: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch(\Throwable $th) {
            DB::rollBack();
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
