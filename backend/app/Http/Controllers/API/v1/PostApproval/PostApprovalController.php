<?php

namespace App\Http\Controllers\API\v1\PostApproval;

use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filters\GlobalFilter;
use App\Http\Resources\Post\PostCollection;
use App\Services\PostApproval\PostApprovalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Spatie\QueryBuilder\AllowedFilter;

class PostApprovalController extends Controller
{
    /**
     * The post approval service instance.
     * 
     * @var PostApprovalService
     */
    private $service;
    
    /**
     * PostApprovalController contructor.
     * 
     * @param PostApprovalService $service The instance of PostApprovalService.
     */
    public function __construct(PostApprovalService $service)
    {
        $this->service = $service;
    }

    /**
     * List of all posts for approval.
     * 
     * @return App\Http\Resources\Post\PostCollection
     */
    public function index(): PostCollection
    {
        if (Gate::denies('view-posts', Post::class)) {
            return response()->json(['message' => 'You are not allowed to access this action'], 403);
        }

        return $this->service->index();
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
