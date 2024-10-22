<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\ResetPasswordRequest;
use App\Services\Authentication\ResetPasswordService;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    /**
     * The email verification service instance.
     * 
     * @var ResetPasswordController
     */
    private $service;
    
    /**
     * EmailVerificationController contructor.
     * 
     * @param ResetPasswordService $service The instance of ResetPasswordService.
     */
    public function __construct(ResetPasswordService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle user send email verification link request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        return $this->service->resetPassword($request);
    }
}
