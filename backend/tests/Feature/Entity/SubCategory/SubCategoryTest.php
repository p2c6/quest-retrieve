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

        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $category = Category::create([
            'name' => 'Category 1',
        ]);

        $response = $this->post('/api/v1/subcategories', [
            'category_id' => $category->id,
            'name' => 'Sample Category'
        ]);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Successfully Subcategory Created.']);
    }

    /**
     * Test user cannot store subcategory with empty category inputs via API.
     * 
     * This test verifies that a user cannot store subcategory with empty category via API endpoint.
     */
    public function test_user_cannot_store_subcategory_with_empty_category(): void
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

        $response = $this->postJson('/api/v1/subcategories', [
            'category_id' => '',
            'name' => 'Sub Category 1'
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['message', 'errors'])
                ->assertJson([
                    'message' => 'The category field is required.', 
                    'errors' => [
                        'category_id' => ['The category field is required.']
                    ]
                ]);
    }

    /**
     * Test user cannot store subcategory with empty subcategory name inputs via API.
     * 
     * This test verifies that a user cannot store subcategory with empty subcategory name via API endpoint.
     */
    public function test_user_cannot_store_subcategory_with_empty_subcategory_name(): void
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

        $category = Category::create([
            'name' => 'Category 1',
        ]);

        $response = $this->postJson('/api/v1/categories', [
            'category_id' => $category->id,
            'name' => ''
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['message', 'errors'])
                ->assertJson([
                    'message' => 'The name field is required.', 
                    'errors' => [
                        'name' => ['The name field is required.']
                    ]
                ]);
    }

    /**
     * Test user cannot store subcategory with all fields are empty via API.
     * 
     * This test verifies that a user cannot store subcategory with all fields are empty via API endpoint.
     */
    public function test_user_cannot_store_subcategory_with_empty_all_fields_are_empty(): void
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
        
        $response = $this->postJson('/api/v1/subcategories', [
            'category_id' => '',
            'name' => ''
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['message', 'errors'])
                ->assertJson([
                    'message' => 'The category field is required. (and 1 more error)', 
                    'errors' => [
                        'category_id' => ['The category field is required.'],
                        'name' => ['The name field is required.']
                    ]
                ]);
    }
    
    /**
     * Test user cannot store subcategory with subcategory name more than 100 hundred characters via API.
     * 
     * This test verifies that a user cannot store subcategory with subcategory name more than 100 characters via API endpoint.
     */
    public function test_user_cannot_store_subcategory_with_subcategory_name_more_than_one_hundred_chars(): void
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

        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $category = Category::create([
            'name' => "Sample Category"
        ]);

        $response = $this->postJson('/api/v1/subcategories', [
            'category_id' => $category->id,
            'name' =>  "Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit incidunt at accusantium. Ex cumque facilis repudiandae eum laudantium sed fuga dolor placeat laborum neque, fugit quia magni? Cupiditate magni corporis error ipsa perferendis soluta velit aperiam ab laborum nemo officia et eum maxime sapiente consequuntur quasi, vero sunt? Suscipit ad qui numquam amet ratione, porro perspiciatis quaerat sequi voluptate vel? Corporis hic optio, voluptatem suscipit quae cupiditate officia dignissimos. Tenetur maxime eos atque itaque quis! Repellat, maiores. Asperiores ullam neque illum perferendis corporis tempora labore ex error architecto debitis sunt laudantium, totam rem repellendus aut! Assumenda, dolore magnam nisi modi corporis quod dolorum eligendi aspernatur!"
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure(['message', 'errors'])
                ->assertJson([
                    'message' => 'The name field must not be greater than 100 characters.', 
                    'errors' => [
                        'name' => ['The name field must not be greater than 100 characters.']
                    ]
                ]);
    }
}
