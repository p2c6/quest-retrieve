<?php

namespace Tests\Feature\Authentication;

use App\Enums\UserType;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
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
    public function test_user_can_register_with_valid_inputs_successfully(): void
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
                'role_id' => $role->id
            ]);

            $response->assertCreated()
                    ->assertJsonStructure(['message'])
                    ->assertJson(['message' => 'Successfully registered an account.']);


        } catch (\Exception $e) {
            $this->fail('Test register with valid inputs error occured' . $e->getMessage());
        }
    }
}
