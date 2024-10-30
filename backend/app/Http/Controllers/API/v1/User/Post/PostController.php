<?php

namespace App\Http\Controllers\API\v1\User\Post\PostController;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserProfile\UpdateUserProfileRequest;
use App\Models\Profile;
use App\Models\User;
use App\Services\Post\PostService\PostService;
use App\Services\UserProfile\UserProfileService;
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
     * @param Illuminate\Http\Request $request The HTTP request object containing role data.
     * @return Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        return $this->service->store($request);
    }
}
