<?php

namespace Tests\Feature\Authentication;

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
            $user = User::factory()->create([
                'password' => bcrypt('password123'),
                'role_id' => 1
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


}
