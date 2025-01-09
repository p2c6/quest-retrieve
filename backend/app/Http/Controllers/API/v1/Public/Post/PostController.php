<?php

namespace App\Http\Controllers\API\v1\Public\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\Post\ClaimRequest;
use App\Models\Post;
use App\Services\Public\Post\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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
     * Handle claim post.
     * 
     * @param App\Models\Post $post The model of the post which needs to be posted.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function claim(Post $post, ClaimRequest $request)
    {   
        return $this->service->claim($post, $request);
    }

    /**
     * Handle return post request.
     * 
     * @param App\Models\Post $post The model of the post which needs to be retrieve.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function return(Post $post, Request $request)
    {
        return $this->service->return($post, $request);
    }
}
