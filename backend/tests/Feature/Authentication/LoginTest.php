<?php

namespace Tests\Feature\Authentication;

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
     * Test user login via API.
     * 
     * This test verifies that a user can be logged-in successfully via API endpoint.
     */
    public function test_user_can_login_successfully(): void
    {
        try {
            $role = Role::where('name', 'Admin')->first();

            if (!$role) {
                $this->fail('Role Admin not found in the database.');
            }

            $user = User::factory()->create([
                'password' => bcrypt('password123'),
                'role_id' => $role->id
            ]);

            $csrf = $this->get('/sanctum/csrf-cookie');

            $csrf->assertCookie('XSRF-TOKEN');

            $response = $this->post('/api/v1/authentication/login', [
                'email' => $user->email,
                'password' => 'password123',
            ]);

            $response->assertCookie('laravel_session')
                      ->assertStatus(200)
                      ->assertJsonStructure(['user', 'message'])
                      ->assertJson(['message' => 'Successfully logged in.'])
                      ->assertSee('user');


        } catch (\Exception $e) {
            $this->fail('An error occured' . $e->getMessage());
        }
    }

    /**
     * Test user cannot login with invalid credentials via API.
     * 
     * This test verifies that a user cannot be logged-in with invalid credentials via API endpoint.
     */
    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        try {
            $role = Role::where('name', 'Admin')->first();

            if (!$role) {
                $this->fail('Role Admin not found in the database.');
            }

            $user = User::factory()->create([
                'password' => bcrypt('password123'),
                'role_id' => $role->id
            ]);

            $csrf = $this->get('/sanctum/csrf-cookie');

            $csrf->assertCookie('XSRF-TOKEN');

            $response = $this->post('/api/v1/authentication/login', [
                'email' => $user->email,
                'password' => 'password1234',
            ]);

            $response->assertCookie('laravel_session')
                      ->assertStatus(401)
                      ->assertJsonStructure(['message'])
                      ->assertJson(['message' => 'The provided credentials are incorrect.']);


        } catch (\Exception $e) {
            $this->fail('An error occured' . $e->getMessage());
        }
    }


}