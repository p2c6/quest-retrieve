<?php

namespace App\Services\Authentication;

use App\Services\Contracts\Authentication\EmailVerificationInterface;
use Illuminate\Http\JsonResponse;

class EmailVerificationService implements EmailVerificationInterface
{
    /**
     * Handle user send email verification notification request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function sendEmailVerification($request): JsonResponse
    {
        try {
            $request->user()->sendEmailVerificationNotification();

            return response()->json(['message' => 'Verification link sent. Please check your e-mail.'], 200);
        } catch(\Throwable $th) {
            info("Error on sending email verification notification: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during sending email verification notification.'
            ], 500);
        }
    }

    /**
     * Handle user verify email request.
     * 
     * @param Illuminate\Foundation\Auth\EmailVerificationRequest $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function verify($request): JsonResponse
    {
        try {
            $request->fulfill();

            return response()->json(['message' => 'Successfully Verified.'], 200);
        } catch(\Throwable $th) {
            info("Error on sending email verification notification: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during sending email verification notification.'
            ], 500);
        }
    }
}
