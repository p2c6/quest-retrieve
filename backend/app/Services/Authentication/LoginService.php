<?php

namespace App\Services\Authentication;

use App\Services\Contracts\LoginInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginService implements LoginInterface
{
    /**
     * Handle user log-in request.
     * 
     * @param App\Http\Requests\Authentication\LoginRequest $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function login($request) : mixed
    {
        try {
            if (Auth::attempt($request->validated())) {
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
