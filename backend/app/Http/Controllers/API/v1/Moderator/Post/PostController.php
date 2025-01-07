<?php

namespace App\Http\Controllers\API\v1\Moderator\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\Moderator\Post\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    
    /**
     * PostController contructor.
     * 
     * @param PostService $service The instance of PostService.
     */
    public function __construct(private PostService $service)
    {
        $this->service = $service;
    }

    /**
     * List of all posts for approval.
     * 
     *  @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        if (Gate::denies('view-any', Post::class)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->index($request->query('keyword'));
    }

    /**
     * Handle approve post.
     * 
     * @param App\Models\Post $post The model of the post which needs to be updated.
     * @return Illuminate\Http\JsonResponse
     */
    public function approve(Post $post) : JsonResponse
    {
        if (Gate::denies('approve-post', $post)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->approve($post);
    }

    /**
     * Handle reject post.
     * 
     * @param App\Models\Post $post The model of the post which needs to be updated.
     * @return Illuminate\Http\JsonResponse
     */
    public function reject(Post $post) : JsonResponse
    {
        if (Gate::denies('reject-post', $post)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->reject($post);
    }
}
