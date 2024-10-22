<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Services\Authentication\SendEmailVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SendEmailVerificationController extends Controller
{
    /**
     * The email verification service instance.
     * 
     * @var SendEmailVerificationController
     */
    private $service;
    
    /**
     * SendEmailVerificationController contructor.
     * 
     * @param SendEmailVerificationService $service The instance of SendEmailVerificationService.
     */
    public function __construct(SendEmailVerificationService $service)
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
}
