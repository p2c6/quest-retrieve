<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Services\Authentication\ForgotPasswordService;
use App\Services\EmailVerification\EmailVerificationService;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
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
    public function __construct(ForgotPasswordService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle user send email verification link request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function sendResetPasswordLink(Request $request): JsonResponse
    {
        return $this->service->sendResetPasswordLink($request);
    }
}
