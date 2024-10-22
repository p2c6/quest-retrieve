<?php

namespace Tests\Feature\EmailVerification;

use App\Enums\UserType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetLinkTest extends TestCase
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
     * Test user can receive reset password notification successfully via API.
     * 
     * This test verifies that the user can reset password notification successfully via API endpoint.
     */
    public function test_user_can_receive_reset_password_notification_successfully(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'email' => 'test111@gmail.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);


        $response = $this->postJson('/api/forgot-password', [
            'email' => $user->email
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'We have emailed your password reset link.']);
    }

    /**
     * Test user cannot receive reset password notification without email provided via API.
     * 
     * This test verifies that the user cannot receive reset password notification without email provided via API endpoint.
     */
    public function test_user_cannot_receive_reset_password_notification_without_no_email_provided(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'email' => 'test111@gmail.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);


        $response = $this->postJson('/api/forgot-password', [
            'email' => ''
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message'])
            ->assertJson(['message' => 'The email field is required.', 
            'errors' => [
                'email' => ['The email field is required.']]
            ]);
    }

    /**
     * Test user cannot receive reset password notification with invalid  email via API.
     * 
     * This test verifies that the user cannot receive reset password notification with invalid email via API endpoint.
     */
    public function test_user_cannot_receive_reset_password_notification_with_invalid_email(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'email' => 'test222@gmail.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($user);


        $response = $this->postJson('/api/forgot-password', [
            'email' => 'test222gmail.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'The email field must be a valid email address.', 
                'errors' => [
                    'email' => ['The email field must be a valid email address.']
                ]
            ]);
    }
}
