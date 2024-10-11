<?php

namespace App\Services\Authentication;

use App\Services\Contracts\LoginInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginService implements LoginInterface
{
    public function login($request) : JsonResponse
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return response()->json([
                    'user' => $request->user(),
                    'message' => 'Successfully logged in.'
                ], 200);
            }

            return response()->json([
                'message' => 'The provided credentials are incorrect.'
            ], 401);
        } catch (ValidationException $validationException) {
            info("Validation Error on user log-in: " . $validationException->getMessage());
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validationException->errors(),
            ], 422);
        } catch (\Throwable $th) {
            info("Error on user log-in: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during login.'
            ], 500);
        }
    }
}
