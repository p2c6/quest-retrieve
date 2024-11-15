<?php

namespace App\Services\Authentication;

use App\Services\Contracts\Authentication\SendResetPasswordLinkInterface;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class SendResetPasswordLinkService implements SendResetPasswordLinkInterface
{
    /**
     * Handle user send reset password link request.
     * 
     * @param Illuminate\Http\Request $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function sendResetPasswordLink($request) : mixed
    {
        try {
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
}
