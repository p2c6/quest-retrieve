<?php

namespace App\Services\Post;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Enums\UserType;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use App\Models\User;
use App\Services\Contracts\Authentication\RegisterInterface;
use App\Services\UserProfile\UserProfileService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostService
{
    /**
     * Handle user store post request.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function store($request) : JsonResponse
    {
        try {
            ;
            Post::create([
                'user_id' => $request->user()->id,
                'type' => $request->type,
                'subcategory_id' => $request->subcategory_id,
                'incident_location' => $request->incident_location,
                'incident_date' => $request->incident_date,
                'status' => PostStatus::PENDING,
            ]);
            
            
            return response()->json([
                'message' => 'Successfully Post Created.'
            ], 201);
        } catch (ValidationException $validationException) {
            info("Validation Error on storing post: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch (\Throwable $th) {
            DB::rollBack();

            info("Error on user register: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during storing post.'
            ], 500);
        }
    }

    /**
     * Handle post update request.
     * 
     * @param App\Models\Post $post The model of the subcategory which needs to be updated.
     * @param App\Http\Requests\Post\UpdatePostRequest $request The HTTP request object containing user data.
     * 
     * @return JsonResponse
     */
    public function update(Post $post, UpdatePostRequest $request) : JsonResponse
    {
        try {
            ;
            $post->update([
                'type' => $request->type,
                'subcategory_id' => $request->subcategory_id,
                'incident_location' => $request->incident_location,
                'incident_date' => $request->incident_date,
            ]);

            return response()->json([
                'message' => 'Successfully Post Updated.'
            ], 200);
        } catch (ValidationException $validationException) {
            info("Validation Error on updating post: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch (\Throwable $th) {
            DB::rollBack();

            info("Error on user register: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during updating post.'
            ], 500);
        }
    }
}