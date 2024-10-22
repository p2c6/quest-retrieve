<?php

namespace App\Services\Authentication;

use App\Models\User;
use App\Services\Contracts\LoginInterface;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordService
{
    /**
     * Handle user reset password request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function sendResetPasswordLink($request) : mixed
    {
        try {
            $request->validate(['email' => 'required|email']);

            $status = Password::sendResetLink(
            $request->only('email')
            );
    
    
            if ($status === Password::RESET_LINK_SENT) {
                return response()->json(['message' => __($status)], 200);
            }

            return response()->json(['email' => [trans($status)]], 422);
        } catch(ValidationException $e) {
            info("Validation Error on user sending reset password link: " . $e->getMessage());
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $th) {
            info("Error on user log-in: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred sending reset password link'
            ], 500);
        }
    
    }
    
    public function resetPassword($request)
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

            if ($status === Password::PASSWORD_RESET) {
                return response()->json(['message' => __($status)], 200);
            }

            return response()->json(['email' => [trans($status)]], 422);
    }
}
