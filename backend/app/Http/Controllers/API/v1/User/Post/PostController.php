<?php

namespace App\Http\Controllers\API\v1\User\Post;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use App\Services\Post\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
     * Handle store post request.
     * 
     * @param App\Http\Requests\Post\StorePostRequest $request The HTTP request object containing role data.
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
     * @return Illuminate\Http\JsonResponse
     */
    public function update(Post $post, UpdatePostRequest $request): JsonResponse
    {
        return $this->service->update($post, $request);
    }
}
