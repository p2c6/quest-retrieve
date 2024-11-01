<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\SendResetPasswordLinkRequest;
use App\Services\Authentication\SendResetPasswordLinkService;
use Illuminate\Http\JsonResponse;

class SendResetPasswordLinkController extends Controller
{
    /**
     * The email verification service instance.
     * 
     * @var SendResetPasswordLinkService
     */
    private $service;
    
    /**
     * EmailVerificationController contructor.
     * 
     * @param SendResetPasswordLinkService $service The instance of SendResetPasswordLinkService.
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
