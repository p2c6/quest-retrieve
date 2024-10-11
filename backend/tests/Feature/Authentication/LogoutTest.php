<?php

namespace Tests\Feature\Authentication;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
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
     * Test user can log-out successfully via API.
     * 
     * This test verifies that a user can log-out successfully via API endpoint.
     */
    public function test_user_can_logout_successfully(): void
    {
        // Seed the role and create a user
        $role = Role::where('name', 'Admin')->first();
        $this->assertNotNull($role, "Role Admin not found in the database.");

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        // Simulate CSRF cookie generation
        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        // Log in the user
        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertStatus(200);

        // Ensure the user is authenticated
        $this->assertAuthenticatedAs($user);

        // Log out the user
        $response = $this->post('/api/v1/authentication/logout');

        // Log the response for debugging
        info('Logout Response: ', $response->json());

        // Check response status and JSON message
        $response->assertStatus(200)
                ->assertJson(['message' => 'Successfully logged out.']);

        // Assert the user is now a guest
        $this->assertGuest();
    }

}
