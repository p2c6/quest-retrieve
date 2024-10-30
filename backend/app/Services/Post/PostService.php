<?php

namespace App\Services\Post;

use App\Enums\PostStatus;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostService
{
    /**
     * List of all posts.
     * 
     * @param App\Models\Post The model of the post which needs to be retrieved.
     * @return App\Http\Resources\Post\PostCollection
     */
    public function index(): PostCollection
    {
        return new PostCollection(Post::paginate());
    }

    /**
     * Show a single post.
     * 
     * @param App\Models\Post $post The model of the subcategory which needs to be retrieved.
     * @return App\Http\Resources\Post\PostResource
     */
    public function show(Post $post): PostResource
    {
        return new PostResource($post);
    }
    
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

    /**
     * Handle delete post request.
     * 
     * @param App\Models\Post $post The model of the post which needs to be deleted.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post): JsonResponse
    {
        try {
            $post->delete();
        
            return response()->json([
                'message' => 'Successfully Post Deleted.'
            ], 200);
        } catch (\Throwable $th) {
            info("Error on deleting post: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during deleting post.'
            ], 500);
        }
    }
}
