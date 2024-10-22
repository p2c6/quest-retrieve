<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Services\Authentication\VerifyService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;

class VerifyController extends Controller
{
    /**
     * The email verification service instance.
     * 
     * @var VerifyController
     */
    private $service;
    
    /**
     * VerifyController contructor.
     * 
     * @param VerifyService $service The instance of VerifyService.
     */
    public function __construct(VerifyService $service)
    {
        $this->service = $service;
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
