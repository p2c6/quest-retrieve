<?php

namespace Tests\Feature\EmailVerification;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seed role.
     * 
     * Set up the test environment by seeding the database with roles.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    /**
     * Test user can reset password with valid credentials successfully via API.
     * 
     * This test verifies that the user can reset password with valid credentials successfully via API endpoint.
     */
    public function test_user_can_reset_password_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test111@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $plainTextToken = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => bcrypt($plainTextToken), 
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $plainTextToken, 
            'password' => 'newpassword1234',
            'password_confirmation' => 'newpassword1234',
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(["message" => 'Your password has been reset.']);
    }

    /**
     * Test user cannot reset password with invalid token via API.
     * 
     * This test verifies that the user cannot reset password with invalid token via API endpoint.
     */
    public function test_user_can_reset_password_with_invalid_token()
    {
        $user = User::factory()->create([
            'email' => 'test111@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $plainTextToken = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => bcrypt($plainTextToken), 
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => 'invalid-token', 
            'password' => 'newpassword1234',
            'password_confirmation' => 'newpassword1234',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['email'])
                ->assertJson(["email" => ['This password reset token is invalid.']]);
    }

    /**
     * Test user cannot reset password with non existing email via API.
     * 
     * This test verifies that the user cannot reset password with non existing email via API endpoint.
     */
    public function test_user_cannot_reset_password_with_non_existing_email()
    {
        User::factory()->create([
            'email' => 'test111@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $plainTextToken = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => 'test111@gmail.com',
            'token' => bcrypt($plainTextToken), 
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'test222@gmail.com',
            'token' => $plainTextToken, 
            'password' => 'newpassword1234',
            'password_confirmation' => 'newpassword1234',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['email'])
                ->assertJson(["email" => ['We can\'t find a user with that email address.']]);
    }

    /**
     * Test user cannot reset password with password mismatch via API.
     * 
     * This test verifies that the user cannot reset password with invalid password mismatch via API endpoint.
     */
    public function test_user_cannot_reset_password_with_password_mismatch()
    {
        $user = User::factory()->create([
            'email' => 'test111@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $plainTextToken = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => 'test111@gmail.com',
            'token' => bcrypt($plainTextToken), 
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $plainTextToken, 
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword1234',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['message'])
                ->assertJson(["errors" => ['password' => ['The password field confirmation does not match.']]]);
    }

    /**
     * Test user cannot reset password with invalid email via API.
     * 
     * This test verifies that the user cannot reset password with invalid email via API endpoint.
     */
    public function test_user_cannot_reset_password_with_invalid_email()
    {
        $user = User::factory()->create([
            'email' => 'test111@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        $plainTextToken = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => 'test111@gmail.com',
            'token' => bcrypt($plainTextToken), 
            'created_at' => now(),
        ]);

        $response = $this->postJson('/api/reset-password', [
            'email' => 'test111gmail.com',
            'token' => $plainTextToken, 
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword1234',
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['message'])
                ->assertJson(["errors" => ['email' => ['The email field must be a valid email address.']]]);
    }
}
