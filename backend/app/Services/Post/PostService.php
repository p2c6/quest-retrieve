<?php

namespace App\Services\Post;

use App\Enums\PostStatus;
use App\Enums\UserType;
use App\Filters\FilterPost;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Mail\ClaimRequested;
use App\Mail\PostCreated;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PostService
{
    /**
     * List of all posts.
     * 
     * @param Illuminate\Http\Request $keyword The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function index($keyword): JsonResponse
    {
        $posts = QueryBuilder::for(Post::class)
        ->where('user_id', auth()->id())
        ->with(['subcategory' => function($q) {
            $q->select('id', 'name');
        }])
        ->select(
            'id',
            'type',
            'subcategory_id', 
            'incident_location', 
            'incident_date',
            'finish_transaction_date',
            'status',
        )
        ->allowedFilters([
            AllowedFilter::custom('keyword', new FilterPost)
        ])
        ->paginate(5)
        ->appends($keyword);
                
        return response()->json($posts);
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
            $post = Post::create([
                'user_id' => $request->user()->id,
                'type' => $request->type,
                'subcategory_id' => $request->subcategory_id,
                'incident_location' => $request->incident_location,
                'incident_date' => $request->incident_date,
                'status' => PostStatus::PENDING,
            ]);

            Mail::to(auth()->user()->email)->send(new PostCreated($post));
            
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
            if ($this->isStillPending($post->status)) {
                $post->update([
                    'type' => $request->type,
                    'subcategory_id' => $request->subcategory_id,
                    'incident_location' => $request->incident_location,
                    'incident_date' => $request->incident_date,
                ]);

                return response()->json([
                    'message' => 'Successfully Post Updated.'
                ], 200);
            }

            return response()->json([
                'message' => 'Cannot update post. The post was already processed.'
            ], 409);

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

    public function isStillPending($status): bool
    {
        if ($status === PostStatus::PENDING) {
            return true;
        }

        return false;
    }

    /**
     * Handle claim post request.
     * 
     * @param App\Models\Post $post The model of the post which needs to be deleted.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function claim(Post $post, $request)
    {
        try {
            Mail::to($post->user->email)->send(new ClaimRequested($post, $request));
            
            return response()->json([
                'message' => 'Successfully Claim Requested.'
            ], 200);
        } catch (\Throwable $th) {
            info("Error on deleting post: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during claiming post.'
            ], 500);
        }
    }
}
