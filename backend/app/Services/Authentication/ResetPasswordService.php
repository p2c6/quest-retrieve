<?php

namespace App\Services\Authentication;

use App\Models\User;
use App\Services\Contracts\Authentication\ResetPasswordInterface;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordService implements ResetPasswordInterface
{
    /**
     * Handle user reset password request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function resetPassword($request) : mixed
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
