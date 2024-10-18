<?php

namespace Tests\Feature\Authentication;

use App\Enums\UserType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LoginTest extends TestCase
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
     * Test user can login with valid credentials via API.
     * 
     * This test verifies that a user can be logged-in with valid credentials via API endpoint.
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertCookie('laravel_session')
                    ->assertStatus(200)
                    ->assertJsonStructure(['user', 'message'])
                    ->assertJson(['message' => 'Successfully logged in.'])
                    ->assertSee('user');
    }

    /**
     * Test user cannot login with invalid credentials via API.
     * 
     * This test verifies that a user cannot be logged-in with invalid credentials via API endpoint.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password1234',
        ]);

        $response->assertCookie('laravel_session')
                    ->assertStatus(401)
                    ->assertJsonStructure(['message'])
                    ->assertJson(['message' => 'The provided credentials are incorrect.']);
    }

    /**
     * Test user cannot login with empty fields via API.
     * 
     * This test verifies that a user cannot be logged-in with empty fields via API endpoint.
     */
    public function test_user_cannot_login_with_empty_fields(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => '',
            'password' => '',
        ]);

        $response->assertCookie('laravel_session')
                    ->assertStatus(422)
                    ->assertJsonStructure(['errors', 'message']);
    }

    /**
     * Test user cannot login with invalid email via API.
     * 
     * This test verifies that a user cannot be logged-in with invalid email via API endpoint.
     */
    public function test_user_cannot_login_with_invalid_email(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'email' => 'test112gmail.com',
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password1234',
        ]);

        $response->assertCookie('laravel_session')
                    ->assertStatus(422)
                    ->assertJsonStructure(['message'])
                    ->assertJson(['message' => 'The email field must be a valid email address.']);
    }
}
