<?php

namespace Tests\Feature\User;

use App\Enums\UserType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
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
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
        $this->artisan('db:seed', ['--class' => 'CategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'SubCategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'PostSeeder']);
    }

    /**
     * Test admin can store user with valid inputs via API.
     * 
     * This test verifies that an admin can store user with valid inputs via API endpoint.
     */
    public function test_admin_can_store_user_with_valid_inputs(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        
        $response = $this->postJson(route('api.v1.users.store'), [
            'email' => 'test000@gmail.com', 
            'password' => 'password123', 
            'password_confirmation' => 'password123', 
            'role_id' => UserType::PUBLIC_USER
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(201)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully User Created.']);
    }

    /**
     * Test admin cannot store user with all empty fields via API.
     * 
     * This test verifies that an admin cannot store user with all empty fields via API endpoint.
     */
    public function test_admin_cannot_store_user_with_all_empty_fields(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        
        $response = $this->postJson(route('api.v1.users.store'), [
            'email' => '', 
            'password' => '', 
            'password_confirmation' => '', 
            'role_id' => ''
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(422)
                ->assertJsonValidationErrors([
                'email',
                'password',
                'role_id',
        ]);
    }

    /**
     * Test other user type is unauthorize to store new user via API.
     * 
     * This test verifies that an admin can store user with valid inputs via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_store_new_user(): void
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

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        
        $response = $this->postJson(route('api.v1.users.store'), [
            'email' => 'test000@gmail.com', 
            'password' => 'password123', 
            'password_confirmation' => 'password123', 
            'role_id' => UserType::PUBLIC_USER
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }
}
