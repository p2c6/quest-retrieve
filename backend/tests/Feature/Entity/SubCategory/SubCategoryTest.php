<?php

namespace Tests\Feature\Entity\SubCategory;

use App\Enums\PostType;
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
     * Test admin can store subcategory with valid inputs via API.
     * 
     * This test verifies that an admin can store subcategory with valid inputs via API endpoint.
     */
    public function test_admin_can_store_subcategory_with_valid_inputs(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

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
     * Test admin cannot store subcategory with empty category inputs via API.
     * 
     * This test verifies that an admin cannot store subcategory with empty category via API endpoint.
     */
    public function test_admin_cannot_store_subcategory_with_empty_category(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

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
     * Test admin cannot store subcategory with empty subcategory name inputs via API.
     * 
     * This test verifies that an admin cannot store subcategory with empty subcategory name via API endpoint.
     */
    public function test_admin_cannot_store_subcategory_with_empty_subcategory_name(): void
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
     * Test admin cannot store subcategory with all fields are empty via API.
     * 
     * This test verifies that an admin cannot store subcategory with all fields are empty via API endpoint.
     */
    public function test_admin_cannot_store_subcategory_with_empty_all_fields_are_empty(): void
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
     * Test admin cannot store subcategory with subcategory name more than 100 hundred characters via API.
     * 
     * This test verifies that an admin cannot store subcategory with subcategory name more than 100 characters via API endpoint.
     */
    public function test_admin_cannot_store_subcategory_with_subcategory_name_more_than_one_hundred_chars(): void
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
     * Test admin cannot store subcategory while category is not exists via API.
     * 
     * This test verifies that an admin cannot store subcategory while category is not exists via API endpoint.
     */
    public function test_admin_cannot_store_subcategory_while_category_is_not_exists(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

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
     * Test admin cannot store subcategory while unauthenticated via API.
     * 
     * This test verifies that an admin cannot store subcategory while unauthenticated via API endpoint.
     */
    public function test_admin_cannot_store_subcategory_while_unauthenticated(): void
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
     * Test admin can update subcategory with valid inputs via API.
     * 
     * This test verifies that an admin can update subcategory with valid inputs via API endpoint.
     */
    public function test_admin_can_update_subcategory_with_valid_inputs(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

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
     * Test admin cannot update subcategory with empty fields via API.
     * 
     * This test verifies that an admin can update subcategory with empty fields via API endpoint.
     */
    public function test_admin_cannot_update_subcategory_with_empty_fields(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

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
     * Test admin cannot update subcategory with subcategory name more than 100 hundred characters via API.
     * 
     * This test verifies that an admin cannot update subcategory with subcategory name more than 100 characters via API endpoint.
     */
    public function test_admin_cannot_update_subcategory_with_subcategory_name_more_than_one_hundred_chars(): void
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
     * Test admin cannot update subcategory while category is not exists via API.
     * 
     * This test verifies that an admin cannot update subcategory while category is not exists via API endpoint.
     */
    public function test_admin_cannot_update_subcategory_while_category_is_not_exists(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

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
     * Test admin cannot update subcategory while unauthenticated via API.
     * 
     * This test verifies that an admin update store subcategory while unauthenticated via API endpoint.
     */
    public function test_admin_cannot_update_subcategory_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

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
     * Test admin can delete subcategory via API.
     * 
     * This test verifies that an admin can delete subcategory via API endpoint.
     */
    public function test_admin_can_delete_subcategory(): void
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
     * Test admin cannot delete while subcategory already associated to post via API.
     * 
     * This test verifies that an admin cannot delete subcategory while subcategory already associated to post via API endpoint.
     */
    public function test_admin_cannot_delete_subcategory_while_already_associated_to_post(): void
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
            'type' => PostType::LOST,
            'subcategory_id' => $subCategoryId,
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
     * Test admin cannot delete subcategory while unauthenticated via API.
     * 
     * This test verifies that an admin cannot delete subcategory while unauthenticated via API endpoint.
     */
    public function test_admin_cannot_delete_subcategory_while_unauthenticated(): void
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
     * Test admin cannot delete subcategory if not existing via API.
     * 
     * This test verifies that an admin cannot delete subcategory if not existing via API endpoint.
     */
    public function test_admin_cannot_delete_subcategory_if_not_existing(): void
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
     * Test admin can retrieve all subcategories via API.
     * 
     * This test verifies that an admin can retrieve all subcategories via API endpoint.
     */
    public function test_admin_can_retrieve_all_subcategories(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

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

        $response = $this->getJson(route('api.v1.subcategories.index'));

        $response->assertStatus(200)
                ->assertJsonStructure(['data', 'links']);
    }

    /**
     * Test admin cannot retrieve all subcategories while unauthenticated via API.
     * 
     * This test verifies that an admin cannot retrieve all subcategories while unauthenticated via API endpoint.
     */
    public function test_admin_cannot_retrieve_all_subcategories_while_unauthenticated(): void
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
     * Test admin can retrieve specific subcategory via API.
     * 
     * This test verifies that an admin can retrieve specific subcategory via API endpoint.
     */
    public function test_admin_can_retrieve_specific_subcategory(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

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

    /**
     * Test admin cannot retrieve specific subcategory while unauthenticated via API.
     * 
     * This test verifies that an admin cannot retrieve specific subcategory while unauthenticated via API endpoint.
     */
    public function test_admin_cannot_retrieve_specific_subcategory_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $category = Category::create([
            'name' => 'Sample Category'
        ]);

        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sub Category 1"
        ]);

        $response = $this->getJson(route('api.v1.categories.show', $subCategory));

        $response->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test admin can retrieve all dropdown subcategories via API.
     * 
     * This test verifies that an admin can retrieve all subcategories via API endpoint.
     */
    public function test_admin_can_retrieve_all_dropdown_subcategories(): void
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

        $response = $this->getJson(route('api.v1.subcategories.dropdown'));

        $response->assertStatus(200);
    }

    /**
     * Test admin cannot retrieve all dropdown subcategories while unauthenticated via API.
     * 
     * This test verifies that an admin cannot retrieve all dropdown subcategories while unauthenticated via API endpoint.
     */
    public function test_admin_cannot_retrieve_all_dropdown_subcategories_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $response = $this->getJson(route('api.v1.subcategories.dropdown'));

        $response->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test other user type is unauthorize to get all subcategories via API.
     * 
     * This test verifies that other admin type is unauthorize to get all subcategories via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_get_all_subcategories(): void
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

        $response = $this->getJson(route('api.v1.subcategories.index'));

        $response->assertCookie('laravel_session')
        ->assertStatus(403)
        ->assertJsonStructure(['message'])
        ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test other user type is unauthorize to get subcategory via API.
     * 
     * This test verifies that other admin type is unauthorize to get subcategory via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_get_subcategory(): void
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
            'name' => 'Category 1'
        ]);

        $usbCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Subcategory 1"
        ]);

        $response = $this->getJson(route('api.v1.subcategories.show', $usbCategory->id));

        $response->assertCookie('laravel_session')
        ->assertStatus(403)
        ->assertJsonStructure(['message'])
        ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test other user type is unauthorize to store subcategory via API.
     * 
     * This test verifies that other admin type is unauthorize to store subcategory via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_store_subcategory(): void
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
            'name' => 'Category 1'
        ]);

        $response = $this->getJson(route('api.v1.subcategories.store'), [
            'category_id' => $category->id,
            'name' => "Subcategory 1"
        ]);

        $response->assertCookie('laravel_session')
        ->assertStatus(403)
        ->assertJsonStructure(['message'])
        ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test other user type is unauthorize to update subcategory via API.
     * 
     * This test verifies that other admin type is unauthorize to update subcategory via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_update_subcategory(): void
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

        $response->assertCookie('laravel_session')
        ->assertStatus(403)
        ->assertJsonStructure(['message'])
        ->assertJson(['message' => 'You are not allowed to access this action']);
    }

}
