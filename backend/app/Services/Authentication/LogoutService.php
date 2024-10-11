<?php

namespace App\Services\Authentication;

use App\Services\Contracts\LogoutInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LogoutService implements LogoutInterface
{
    /**
     * Handle user log-out request.
     * 
     *  @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function logout($request) : JsonResponse
    {
        try {
            // Log out the user using the web guard
            Auth::guard('web')->logout(); // This is for session-based logout

            // Invalidate the session and regenerate token
            $request->session()->invalidate(); 
            $request->session()->regenerateToken();
            
            return response()->json([
                'message' => 'Successfully logged out.'
            ], 200);
            
        } catch (\Throwable $th) {
            info("Error on user log-out: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during logout.'
            ], 500);
        }
    }


}
