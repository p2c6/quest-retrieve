<?php

namespace App\Services\Authentication;

use App\Enums\UserType;
use App\Models\User;
use App\Services\Contracts\Authentication\RegisterInterface;
use App\Services\UserProfile\UserProfileService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RegisterService implements RegisterInterface
{
    /**
     * The UserProfileService instance.
     * 
     * @var UserProfileService
     */
    private $service;

    /**
     * The UserProfileService constructor.
     * 
     * @param UserProfileService $service The instance of UserProfileService
     */
    public function __construct(UserProfileService $service)
    {
        $this->service = $service;
    }
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
            $user = User::create([
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role_id' => UserType::PUBLIC_USER
            ]);

            $this->service->storeUserProfile($user->id, $request);

            event(new Registered($user));

            Auth::login($user);
            
            return response()->json([
                'message' => 'Successfully registered an account.'
            ], 201);
        } catch (ValidationException $validationException) {
            info("Validation Error on user register: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch (\Throwable $th) {
            info("Error on user register: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during register.'
            ], 500);
        }
    }
}
