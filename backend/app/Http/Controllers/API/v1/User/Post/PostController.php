<?php

namespace App\Http\Controllers\API\v1\User\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Models\Post;
use App\Services\Post\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * The post service instance.
     * 
     * @var PostService
     */
    private $service;
    
    /**
     * PostController contructor.
     * 
     * @param PostService $service The instance of PostService.
     */
    public function __construct(PostService $service)
    {
        $this->service = $service;
    }
    
    /**
     * List of all posts.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->service->index($request->query('keyword'));
    }

    /**
     * Show a single post.
     * 
     * @param App\Models\Post $post The model of the subcategory which needs to be retrieved.
     * @return App\Http\Resources\Post\PostResource
     */
    public function show(Post $post): PostResource
    {
        return $this->service->show($post);
    }

    /**
     * Handle store post request.
     * 
     * @param App\Http\Requests\Post\StorePostRequest $request The HTTP request object containing role data.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }

    /**
     * Handle update post request.
     * 
     * @param App\Http\Requests\Post\UpdatePostRequest $request The HTTP request object containing role data.
     * @param App\Models\Post $post The model of the subcategory which needs to be updated.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Post $post, UpdatePostRequest $request): JsonResponse
    {
        if (Gate::denies('update', $post)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }
        
        return $this->service->update($post, $request);
    }

    /**
     * Handle delete post.
     * 
     * @param App\Models\Post $post The model of the subcategory which needs to be deleted.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function destroy(Post $post): JsonResponse
    {
        if (Gate::denies('delete', $post)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->destroy($post);
    }

    /**
     * Handle claim post.
     * 
     * @param App\Models\Post $post The model of the post which needs to be posted.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function claim(Post $post, Request $request)
    {   
        return $this->service->claim($post, $request);
    }

    /**
     * Handle mark as done post.
     * 
     * @param App\Models\Post $post The model of the post which needs status to be updated.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function markAsDone(Post $post): JsonResponse
    {
        if (Gate::denies('mark-as-done', $post)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->markAsDone($post);
    }
}
