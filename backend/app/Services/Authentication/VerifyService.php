<?php

namespace App\Services\Authentication;

use App\Mail\Welcome;
use App\Models\User;
use App\Services\Contracts\Authentication\VerifyInterface;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VerifyService implements VerifyInterface
{
    /**
     * Handle user verify email request.
     * 
     * @param Illuminate\Foundation\Auth\EmailVerificationRequest $request The HTTP request object containing user data.
     * @return Illuminate\Http\JsonResponse
     */
    public function verify($request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->fulfill();

            $this->sendWelcomeMail($request->user()->email);

            DB::commit();

            activity()
                ->performedOn($request->user())
                ->log('User request verification link');

            return response()->json(['message' => 'Successfully Verified.'], 200);
        } catch(\Throwable $th) {
            DB::rollBack();
            info("Error on sending email verification notification: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during email verification.'
            ], 500);
        }
    }

    public function sendWelcomeMail($email)
    {
        try {
            $user = User::where('email', $email)->firstOrFail();

            Mail::to($email)->send(new Welcome($user));
        } catch(\Throwable $e) {
            info('Error sending welcome mail: ' . $e->getMessage());
        }
    }
}
