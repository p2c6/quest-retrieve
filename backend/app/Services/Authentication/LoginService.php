<?php
namespace App\Services\Authentication;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use LoginInteface;

class LoginService implements LoginInteface
{
    public function login($request) : JsonResponse
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
     
            if (Auth::attempt($credentials)) {
                return response()->json([
                    'user' => $request->user(),
                    'message' => 'Successfully logged in.'
                ], 200);
            }
    
            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}