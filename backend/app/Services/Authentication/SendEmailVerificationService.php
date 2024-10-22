<?php

namespace App\Services\Authentication;

use App\Services\Contracts\Authentication\SendEmailVerificationInterface;
use Illuminate\Http\JsonResponse;

class SendEmailVerificationService implements SendEmailVerificationInterface
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
}
