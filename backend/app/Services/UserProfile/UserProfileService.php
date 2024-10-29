<?php

namespace App\Services\UserProfile;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class UserProfileService
{
    /**
     * Store user profile upon registration.
     * 
     * @param int $userId The user id of the newly created user.
     * @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return mixed
     */
    public function storeUserProfile(int $userId, $request): mixed
    {
        try {
            Profile::create([
                'user_id' => $userId,
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'birthday' => $request->birthday,
                'contact_no' => $request->contact_no,
            ]);

            return response()->json([
                'message' => 'Successfully user profile saved.'
            ], 201);
        } catch (ValidationException $validationException) {
            info("Validation Error on user profile save: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch (\Throwable $th) {
            info("Error on user register: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during user profile save.'
            ], 500);
        }
    }

    /**
     * Update user profile.
     * 
     * @param App\Models\User $user The user profile needs to be updated.
     * @param \Illuminate\Http\Request $request The HTTP request object containing user data.
     * @return mixed
     */
    public function update(User $user, $request): mixed
    {
        try {

            $user->profile()->update([
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'birthday' => $request->birthday,
                'contact_no' => $request->contact_no,
            ]);

            return response()->json([
                'message' => 'Successfully User Profile Updated.'
            ], 201);
        } catch (ValidationException $validationException) {
            info("Validation Error on user profile update: " . $validationException->getMessage());
            return response()->json(['errors' => $validationException->errors()], 422);
        } catch (\Throwable $th) {
            info("Error on user register: " . $th->getMessage());
            return response()->json([
                'message' => 'An error occurred during user profile update.'
            ], 500);
        }
    }
}
