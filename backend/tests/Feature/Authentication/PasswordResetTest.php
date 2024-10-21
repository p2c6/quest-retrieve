<?php

namespace Tests\Feature\EmailVerification;

use App\Enums\UserType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Mockery;
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
     * Test user can reset password successfully via API.
     * 
     * This test verifies that the user can reset password successfully via API endpoint.
     */
    public function test_password_reset_workflow()
    {
        // Create a user
        $user = User::factory()->create([
            'email' => 'test111@gmail.com',
            'password' => bcrypt('password123'),
        ]);

        // Step 1: Send the password reset link
        $this->postJson('/api/forgot-password', [
            'email' => $user->email,
        ])->assertStatus(200);

        // Step 2: Check that the token was created
        $this->assertDatabaseHas('password_resets', [
            'email' => $user->email,
        ]);

        // Step 3: Get the token
        $token = DB::table('password_resets')->where('email', $user->email)->value('token');
        $this->assertNotNull($token, 'Password reset token should be generated.');

        // Step 4: Attempt to reset the password
        $response = $this->postJson('/api/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'newpassword1234',
            'password_confirmation' => 'newpassword1234',
        ]);

        // Step 5: Log response if there's an error
        if ($response->status() === 422) {
            Log::info('Reset Password Response:', $response->json());
        }

        // Step 6: Assert response
        $response->assertStatus(200);
        $this->assertTrue(Hash::check('newpassword1234', $user->fresh()->password));
    }
}
