<?php

namespace Tests\Feature\Entity\SubCategory;

use App\Enums\UserType;
use App\Models\Category;
use App\Models\Post;
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

    /**
     * Test user cannot store subcategory while category is not exists via API.
     * 
     * This test verifies that a user cannot store subcategory while category is not exists via API endpoint.
     */
    public function test_user_cannot_store_subcategory_while_category_is_not_exists(): void
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

        $response = $this->postJson('/api/v1/subcategories', [
            'category_id' => 0,
            'name' =>  "Sub Category 1"
        ]);

        $response->assertStatus(404)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Cannot store subcategory. Category not found.']);
    }

    /**
     * Test user cannot store subcategory while unauthenticated via API.
     * 
     * This test verifies that a user cannot store subcategory while unauthenticated via API endpoint.
     */
    public function test_user_cannot_store_subcategory_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $category = Category::create([
            'name' => 'Category 1',
        ]);

        $response = $this->postJson('/api/v1/subcategories', [
            'category_id' => $category->id,
            'name' => 'Sample Category'
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user can update subcategory with valid inputs via API.
     * 
     * This test verifies that a user can update subcategory with valid inputs via API endpoint.
     */
    public function test_user_can_update_subcategory_with_valid_inputs(): void
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

        $categoryName = "Category One";
        
        $categoryOne = Category::create([
            'name' => $categoryName
        ]);

        $subCategoryName = "Sub Category One";

        $subCategory = Subcategory::create([
            'category_id' => $categoryOne->id,
            'name' => $subCategoryName,
        ]);

        $updatedCategoryId = 2;
        $updatedSubCategoryName = "Sub Category Updated";

        $response = $this->putJson(route('api.v1.subcategories.update', $subCategory->id), [
            'category_id' => $updatedCategoryId,
            'name' => $updatedSubCategoryName
        ]);

        $updatedSubCategory = Subcategory::find($subCategory->id);

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJson(['message' => 'Successfully Subcategory Updated.']);
    }

    /**
     * Test user cannot update subcategory with empty fields via API.
     * 
     * This test verifies that a user can update subcategory with empty fields via API endpoint.
     */
    public function test_user_cannot_update_subcategory_with_empty_fields(): void
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

        $categoryName = "Category One";
        
        $categoryOne = Category::create([
            'name' => $categoryName
        ]);

        $subCategoryName = "Sub Category One";

        $subCategory = Subcategory::create([
            'category_id' => $categoryOne->id,
            'name' => $subCategoryName,
        ]);

        $response = $this->putJson(route('api.v1.subcategories.update', $subCategory->id), [
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
     * Test user cannot update subcategory with subcategory name more than 100 hundred characters via API.
     * 
     * This test verifies that a user cannot update subcategory with subcategory name more than 100 characters via API endpoint.
     */
    public function test_user_cannot_update_subcategory_with_subcategory_name_more_than_one_hundred_chars(): void
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

        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Category"
        ]);

        $response = $this->putJson(route('api.v1.subcategories.update',$subCategory->id), [
            'category_id' => $category->id,
            'name' => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio porro corporis deserunt in recusandae! Corporis, cumque, sed perferendis repellat consequatur explicabo asperiores aliquam dolores, maiores nemo harum nihil ratione dignissimos illum perspiciatis deleniti neque ab placeat praesentium earum eveniet minima maxime at! Neque reiciendis culpa distinctio alias harum consequatur nesciunt perferendis enim amet? Nobis nemo quisquam ducimus dolorum sint, voluptatum minima eum, nostrum delectus iure aliquam vero impedit enim in. Ullam rerum totam nostrum repellendus consectetur error pariatur obcaecati libero? Illo temporibus eum ullam consequatur veniam dolorum minima saepe. Accusantium incidunt laudantium sequi veniam, in temporibus libero quis, qui magnam harum tenetur nobis animi? Eius!"
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

    /**
     * Test user cannot update subcategory while category is not exists via API.
     * 
     * This test verifies that a user cannot update subcategory while category is not exists via API endpoint.
     */
    public function test_user_cannot_update_subcategory_while_category_is_not_exists(): void
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

        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Category"
        ]);

        $response = $this->putJson(route('api.v1.subcategories.update',$subCategory->id), [
            'category_id' => 0,
            'name' => "SubCategory One"
        ]);

        $response->assertStatus(404)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Cannot update subcategory. Category not found.']);
    }

    /**
     * Test user cannot update subcategory while unauthenticated via API.
     * 
     * This test verifies that a user update store subcategory while unauthenticated via API endpoint.
     */
    public function test_user_cannot_update_subcategory_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $category = Category::create([
            'name' => "Sample Category"
        ]);

        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Category"
        ]);

        $response = $this->putJson(route('api.v1.subcategories.update',$subCategory->id), [
            'category_id' => $category->id,
            'name' => "Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odio porro corporis deserunt in recusandae! Corporis, cumque, sed perferendis repellat consequatur explicabo asperiores aliquam dolores, maiores nemo harum nihil ratione dignissimos illum perspiciatis deleniti neque ab placeat praesentium earum eveniet minima maxime at! Neque reiciendis culpa distinctio alias harum consequatur nesciunt perferendis enim amet? Nobis nemo quisquam ducimus dolorum sint, voluptatum minima eum, nostrum delectus iure aliquam vero impedit enim in. Ullam rerum totam nostrum repellendus consectetur error pariatur obcaecati libero? Illo temporibus eum ullam consequatur veniam dolorum minima saepe. Accusantium incidunt laudantium sequi veniam, in temporibus libero quis, qui magnam harum tenetur nobis animi? Eius!"
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user can delete subcategory via API.
     * 
     * This test verifies that a user can delete subcategory via API endpoint.
     */
    public function test_user_can_delete_subcategory(): void
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

        $category = Category::create([
            'name' => "Sample Category",
        ]);

        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory",
        ]);
        
        $response = $this->deleteJson(route('api.v1.subcategories.destroy', $subCategory->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Successfully Subcategory Deleted.', 
                ]);
    }
    

    /**
     * Test user cannot delete while subcategory already associated to post via API.
     * 
     * This test verifies that a user cannot delete subcategory while subcategory already associated to post via API endpoint.
     */
    public function test_user_cannot_delete_subcategory_while_already_associated_to_post(): void
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

        $category = Category::create([
            'name' => "Sample Category",
        ]);

        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory",
        ]);

        $subCategoryId = $subCategory->id;
        
        Post::create([
            'user_id' => $user->id,
            'subcategory_id' => $subCategoryId,
            'name' => 'Test Post',
            'incident_location' => 'Quezon City',
            'incident_date' => '2024-05-06',
            'finish_transaction_date' => '2024-05-07',
            'expiration_date' =>  '2024-06-06',
            'status' => 'finish',
        ]);

        $this->assertDatabaseHas('posts', [
            'subcategory_id' => $subCategory->id,
        ]);

        $response = $this->deleteJson(route('api.v1.subcategories.destroy', $subCategory));

        $response->assertCookie('laravel_session')
                ->assertStatus(409)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Cannot delete subcategory. There are posts associated with this subcategory.'
                ]);
    }

    /**
     * Test user cannot delete subcategory while unauthenticated via API.
     * 
     * This test verifies that a user cannot delete subcategory while unauthenticated via API endpoint.
     */
    public function test_user_cannot_delete_subcategory_while_unauthenticated(): void
    {
        $category = Category::create([
            'name' => "Category 1"
        ]);

        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sub Category 1"
        ]);

        $response = $this->deleteJson(route('api.v1.subcategories.destroy', $subCategory));

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user cannot delete subcategory if not existing via API.
     * 
     * This test verifies that a user cannot delete subcategory if not existing via API endpoint.
     */
    public function test_user_cannot_delete_subcategory_if_not_existing(): void
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

        $response = $this->deleteJson(route('api.v1.subcategories.destroy', 0));

        $response->assertCookie('laravel_session')
                ->assertStatus(404);
    }

    /**
     * Test user can retrieve all subcategories via API.
     * 
     * This test verifies that a user can retrieve all subcategories via API endpoint.
     */
    public function test_user_can_retrieve_all_subcategories(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->getJson('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $response = $this->getJson('/api/v1/subcategories');

        $response->assertStatus(200)
                ->assertJsonStructure(['data', 'links', 'meta']);
    }

    /**
     * Test user cannot retrieve all subcategories while unauthenticated via API.
     * 
     * This test verifies that a user cannot retrieve all subcategories while unauthenticated via API endpoint.
     */
    public function test_user_cannot_retrieve_all_subcategories_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $response = $this->getJson('/api/v1/subcategories');

        $response->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user can retrieve specific subcategory via API.
     * 
     * This test verifies that a user can retrieve specific subcategory via API endpoint.
     */
    public function test_user_can_retrieve_specific_subcategory(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
        ]);

        $this->getJson('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $category = Category::create([
            'name' => 'Sample Category'
        ]);

        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sub Category 1"
        ]);

        $response = $this->getJson(route('api.v1.subcategories.show', $subCategory));

        $response->assertStatus(200)
                ->assertJsonStructure(['data']);
    }

}
