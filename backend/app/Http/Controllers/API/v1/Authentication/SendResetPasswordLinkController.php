<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\SendResetPasswordLinkRequest;
use App\Services\Authentication\ForgotPasswordService;
use App\Services\Authentication\SendResetPasswordLinkService;
use App\Services\EmailVerification\EmailVerificationService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class SendResetPasswordLinkController extends Controller
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
    public function __construct(SendResetPasswordLinkService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle user send email verification link request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function sendResetPasswordLink(SendResetPasswordLinkRequest $request): JsonResponse
    {
        return $this->service->sendResetPasswordLink($request);
    }
}