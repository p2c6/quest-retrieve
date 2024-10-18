<?php

namespace Tests\Feature\EmailVerification;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
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
     * Test user can receive email verification notification successfully via API.
     * 
     * This test verifies that the user can receive email verification notification successfully via API endpoint.
     */
    public function test_user_can_receive_email_verification_notification_successfully(): void
    {
        try {
            $role = Role::where('name', 'Admin')->first();

            if (!$role) {
                $this->fail('Role Public User not found in the database.');
            }

            $csrf = $this->get('/sanctum/csrf-cookie');

            $csrf->assertCookie('XSRF-TOKEN');

            $user = User::factory()->create([
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $this->actingAs($user);


            $response = $this->postJson('/api/verification/email/verification-notification');

            $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Verification link sent. Please check your e-mail.']);


        } catch (\Exception $e) {
            $this->fail('Test receive email verification notification error ' . $e->getMessage());
        }
    }

    /**
     * Test user cannot receive email verification notification on failure via API.
     * 
     * This test verifies that the user cannot receive email verification notification on failure via API endpoint.
     */
    public function test_user_can_receive_email_verification_notification_failure(): void
    {
        try {
            $role = Role::where('name', 'Admin')->first();

            if (!$role) {
                $this->fail('Role Public User not found in the database.');
            }

            $csrf = $this->get('/sanctum/csrf-cookie');

            $csrf->assertCookie('XSRF-TOKEN');

            $user = User::factory()->create([
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);

            $this->actingAs($user);

            Mail::shouldReceive('to')
            ->andThrow(new \Exception('Simulated email sending failure'));

            $response = $this->postJson('/api/verification/email/verification-notification');

            $response->assertStatus(500)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'An error occurred during sending email verification notification.']);
        } catch (\Exception $e) {
            $this->fail('Test receive email verification notification error ' . $e->getMessage());
        }
    }

    /**
     * Test user can verify email successfully via API.
     * 
     * This test verifies that the user can verify email successfully via API endpoint.
     */
    public function test_user_can_verify_email_successfully(): void
    {
        try {
            $role = Role::where('name', 'Admin')->first();
    
            if (!$role) {
                $this->fail('Role Admin not found in the database.');
            }
    
            $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');
    
            $user = User::factory()->create([
                'password' => bcrypt('password123'),
                'role_id' => $role->id,
            ]);
    
            $user->sendEmailVerificationNotification();
    
            $hash = sha1($user->email); 
    
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id, 'hash' => $hash]
            );
    
            $this->actingAs($user);
    
            $response = $this->get($verificationUrl);
    
            $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully Verified.']);
    
        } catch (\Exception $e) {
            $this->fail('Test verify email error occurred: ' . $e->getMessage());
        }
    }
    
}
