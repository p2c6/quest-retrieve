<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Services\Authentication\EmailVerificationService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * The email verification service instance.
     * 
     * @var EmailVerificationController
     */
    private $service;
    
    /**
     * EmailVerificationController contructor.
     * 
     * @param EmailVerificationService $service The instance of EmailVerificationService.
     */
    public function __construct(EmailVerificationService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle user send email verification link request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function sendEmailVerification(Request $request): JsonResponse
    {
        return $this->service->sendEmailVerification($request);
    
    }

    /**
     * Handle user verify email request.
     * 
     * @param Illuminate\Foundation\Auth\EmailVerificationRequest $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function verify(EmailVerificationRequest $request): JsonResponse
    {
        return $this->service->verify($request);
    }
}
