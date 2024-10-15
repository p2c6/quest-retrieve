<?php

namespace App\Services\Authentication;

use App\Enums\UserType;
use App\Models\User;
use App\Services\Contracts\RegisterInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RegisterService implements RegisterInterface
{
    /**
     * Handle user log-in request.
     * 
     * @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     * 
     * @return mixed
     */
    public function register($request) : JsonResponse
    {
        try {
            User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => UserType::PublicUser
            ]);
            
            return response()->json([
                'message' => 'Successfully registered an account.'
            ], 200);
        } catch (ValidationException $validationException) {
            info("Validation Error on user register: " . $validationException->getMessage());
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $validationException->errors(),
            ], 422);
        } catch (\Throwable $th) {
            info("Error on user log-in: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during register.'
            ], 500);
        }
    }
}
