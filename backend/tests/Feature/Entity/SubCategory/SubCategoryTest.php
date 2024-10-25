<?php

namespace Tests\Feature\Entity\SubCategory;

use App\Enums\UserType;
use App\Models\Category;
use App\Models\Role;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubCategoryTest extends TestCase
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
        $this->artisan('db:seed', ['--class' => 'CategorySeeder']);
        $this->artisan('db:seed', ['--class' => 'SubCategorySeeder']);
    }

    /**
     * Test user can store subcategory with valid inputs via API.
     * 
     * This test verifies that a user can store subcategory with valid inputs via API endpoint.
     */
    public function test_user_can_store_subcategory_with_valid_inputs(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        // Log in the user
        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $response = $this->post('/api/v1/subcategories', [
            'name' => 'Sample Category'
        ]);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Successfully Subcategory Created.']);
    }
}
