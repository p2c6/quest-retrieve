<?php

namespace Tests\Feature\Authentication;

use App\Enums\UserType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
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
     * Test user can register with valid inputs successfully via API.
     * 
     * This test verifies that a user can register with valid inputs successfully via API endpoint.
     */
    public function test_user_can_register_with_valid_inputs(): void
    {
        try {
            $role = Role::where('name', 'Admin')->first();

            if (!$role) {
                $this->fail('Role Public User not found in the database.');
            }

            $csrf = $this->get('/sanctum/csrf-cookie');

            $csrf->assertCookie('XSRF-TOKEN');

            $response = $this->postJson('/api/v1/authentication/register', [
                'email' => 'testinguser1@gmail.com',
                'password' => 'password1234',
            ]);

            $response->assertStatus(201)
                    ->assertJsonStructure(['message'])
                    ->assertJson(['message' => 'Successfully registered an account.']);


        } catch (\Exception $e) {
            $this->fail('Test register with valid inputs error occured' . $e->getMessage());
        }
    }

    /**
     * Test user cannot register if the email used is already existing via API.
     * 
     * This test verifies that a user cannot register if the email used in registration 
     * is already exists via API endpoint.
     */
    public function test_user_cannot_register_with_existing_email(): void
    {
        try {
            $role = Role::where('name', 'Admin')->first();

            if (!$role) {
                $this->fail('Role Public User not found in the database.');
            }

            $csrf = $this->get('/sanctum/csrf-cookie');

            $csrf->assertCookie('XSRF-TOKEN');

            User::factory()->create([
                'email' => 'testinguser1@gmail.com',
                'password' => bcrypt('password123'),
                'role_id' => $role->id
            ]);

            $response = $this->postJson('/api/v1/authentication/register', [
                'email' => 'testinguser1@gmail.com',
                'password' => 'password1234',
            ]);

            $response->assertStatus(422)
                    ->assertJsonStructure(['message'])
                    ->assertJson(['message' => 'The email has already been taken.']);
        } catch (\Exception $e) {
            $this->fail('Test cannot register with existing email used error occured' . $e->getMessage());
        }
    }

    /**
     * Test user cannot register if the password is shorter than 8 characters via API.
     * 
     * This test verifies that a user cannot register if the password is shorter 8 characters.
     */
    public function test_user_cannot_register_with_short_password(): void
    {
            $role = Role::where('name', 'Admin')->first();

            if (!$role) {
                $this->fail('Role Public User not found in the database.');
            }

            $csrf = $this->get('/sanctum/csrf-cookie');

            $csrf->assertCookie('XSRF-TOKEN');

            $response = $this->postJson('/api/v1/authentication/register', [
                'email' => 'testinguser123@gmail.com',
                'password' => 'pass',
            ]);

            $response->assertStatus(422)
                    ->assertJsonStructure(['message'])
                    ->assertJson(['message' => 'The password field must be at least 8 characters.']);
    }
}
