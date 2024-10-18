<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Authentication\ForgotPasswordService;
use App\Services\EmailVerification\EmailVerificationService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
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
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
     
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            });

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => __($status)], 200);
            }

            return response()->json(['email' => [trans($status)]], 422);
    }
}
