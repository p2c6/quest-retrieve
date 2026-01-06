<?php

namespace App\Http\Controllers\API\v1\User\UserProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserProfile\UpdateUserProfilePasswordRequest;
use App\Http\Requests\UserProfile\UpdateUserProfileRequest;
use App\Models\User;
use App\Services\UserProfile\UserProfileService;
use Illuminate\Http\JsonResponse;

class UserProfileController extends Controller
{
    /**
     * The user profile service instance.
     * 
     * @var UserProfileService
     */
    private $service;
    
    /**
     * UserProfileController contructor.
     * 
     * @param UserProfileService $service The instance of UserProfileService.
     */
    public function __construct(UserProfileService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle user profile update request.
     * 
     * @param App\Models\User $profile The user profile needs to be updated.
     * @param App\Http\Requests\UserProfile\UpdateUserProfileRequest $request The HTTP request object containing user data.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function update(User $user, UpdateUserProfileRequest $request): JsonResponse
    {
        return $this->service->update($user, $request);
    }

    /**
     * Handle update password on user profile.
     * 
     * @param App\Models\User $profile The user profile needs to be updated.
     * @param App\Http\Requests\UserProfile\UpdateUserProfileRequest $request The HTTP request object containing user data.
     * 
     * @return Illuminate\Http\JsonResponse
     */
    public function updatePassword(User $user, UpdateUserProfilePasswordRequest $request): JsonResponse
    {
        return $this->service->updatePassword($user, $request);
    }
}
