<?php

namespace App\Http\Controllers\API\v1\Public\Post;

use App\Http\Controllers\Controller;
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
}
