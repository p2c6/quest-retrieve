<?php

namespace Tests\Feature\EmailVerification;

use App\Enums\UserType;
use App\Models\Role;
use App\Models\User;
use App\Services\EmailVerification\EmailVerificationService;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Mockery;
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
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

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
    }

    /**
     * Test user cannot receive email verification notification via API.
     * 
     * This test verifies that the user cannot receive email verification notification via API endpoint.
     */
    public function test_user_can_receive_email_verification_notification_failure(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        // Get CSRF cookie
        $csrf = $this->get('/sanctum/csrf-cookie');
        $csrf->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);

        Notification::shouldReceive('send')
            ->once()
            ->with($user, Mockery::type(VerifyEmail::class))
            ->andThrow(new \Exception('Simulated email sending failure'));

        $response = $this->postJson('/api/verification/email/verification-notification');

        $response->assertStatus(500)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'An error occurred during sending email verification notification.']);
    }

    /**
     * Test user can verify email successfully via API.
     * 
     * This test verifies that the user can verify email successfully via API endpoint.
     */
    public function test_user_can_verify_email_successfully(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);

        $user->sendEmailVerificationNotification();

        $hash = sha1($user->email); 

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => $hash]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'Successfully Verified.']);
    }

    /**
     * Test user cannot verify email via API.
     * 
     * This test verifies that the user cannot verify email via API endpoint.
     */
    public function test_user_can_verify_email_failure(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'email_verified_at' => null, 
        ]);

        $this->actingAs($user);

        $invalidHash = 'invalid-hash';

        $signedUrl = URL::temporarySignedRoute(
            'verification.verify', 
            now()->addMinutes(30),
            ['id' => 0, $invalidHash] 
        );

        $response = $this->get($signedUrl);
    
        $response->assertStatus(403);
    }
}
