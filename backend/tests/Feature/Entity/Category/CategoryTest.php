<?php

namespace Tests\Feature\Entity\Category;

use App\Enums\UserType;
use App\Models\Category;
use App\Models\Role;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
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
    }

    /**
     * Test user can store category with valid inputs via API.
     * 
     * This test verifies that a user can store category with valid inputs via API endpoint.
     */
    public function test_user_can_store_category_with_valid_inputs(): void
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

        $response = $this->post('/api/v1/categories', [
            'name' => 'Sample Category'
        ]);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Successfully Category Created.']);
    }

    /**
     * Test user cannot store category with empty role name inputs via API.
     * 
     * This test verifies that a user cannot store category with empty category name via API endpoint.
     */
    public function test_user_cannot_store_category_with_empty_category_name(): void
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

        $response = $this->postJson('/api/v1/categories', [
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
     * Test user cannot store category with category name more than 100 hundred characters via API.
     * 
     * This test verifies that a user cannot store category with category name more than 100 characters via API endpoint.
     */
    public function test_user_cannot_store_category_with_category_name_more_than_one_hundred_chars(): void
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

        $response = $this->postJson('/api/v1/categories', [
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
     * Test user cannot store category while unauthenticated via API.
     * 
     * This test verifies that a user cannot store category while unauthenticated via API endpoint.
     */
    public function test_user_cannot_store_category_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $response = $this->postJson('/api/v1/categories', [
            'name' =>  "Sample Role"
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user can update category with valid inputs via API.
     * 
     * This test verifies that a user can update category with valid inputs via API endpoint.
     */
    public function test_user_can_update_category_with_valid_inputs(): void
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

        $newCategoryName = 'Old Category';
        $updatedCategoryName = 'New Category';

        $newCategory = Category::create([
            'name' => $newCategoryName
        ]);

        $response = $this->putJson(route('api.v1.categories.update', $newCategory->id), [
            'name' => $updatedCategoryName
        ]);

        $updatedCategory = Category::find($newCategory->id);

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJson(['message' => 'Successfully Category Updated.']);

        $this->assertNotEquals($newCategoryName, $updatedCategory->name, 'Values should not be equals');
    }

    /**
     * Test user cannot update category with empty category name inputs via API.
     * 
     * This test verifies that a user cannot update category with empty category name via API endpoint.
     */
    public function test_user_cannot_update_category_with_empty_fields(): void
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

        $newCategoryName = 'Old Category';

        $newRole = Category::create([
            'name' => $newCategoryName
        ]);

        $response = $this->putJson(route('api.v1.categories.update', $newRole->id), [
            'name' => ''
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(422)
                ->assertJson([
                    'message' => 'The name field is required.', 
                    'errors' => [
                        'name' => ['The name field is required.']
                    ]
                ]);
    }

    /**
     * Test user cannot store category with category name more than 100 hundred characters via API.
     * 
     * This test verifies that a user cannot store category with category name more than 100 characters via API endpoint.
     */
    public function test_user_cannot_update_category_with_category_name_more_than_one_hundred_chars(): void
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

        $newCategoryName = 'Old Category';

        $newRole = Category::create([
            'name' => $newCategoryName
        ]);

        $response = $this->putJson(route('api.v1.categories.update', $newRole->id), [
            'name' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Odit incidunt at accusantium. Ex cumque facilis repudiandae eum laudantium sed fuga dolor placeat laborum neque, fugit quia magni? Cupiditate magni corporis error ipsa perferendis soluta velit aperiam ab laborum nemo officia et eum maxime sapiente consequuntur quasi, vero sunt? Suscipit ad qui numquam amet ratione, porro perspiciatis quaerat sequi voluptate vel? Corporis hic optio, voluptatem suscipit quae cupiditate officia dignissimos. Tenetur maxime eos atque itaque quis! Repellat, maiores. Asperiores ullam neque illum perferendis corporis tempora labore ex error architecto debitis sunt laudantium, totam rem repellendus aut! Assumenda, dolore magnam nisi modi corporis quod dolorum eligendi aspernatur!'
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(422)
                ->assertJsonStructure(['message', 'errors'])
                ->assertJson([
                    'message' => 'The name field must not be greater than 100 characters.', 
                    'errors' => [
                        'name' => ['The name field must not be greater than 100 characters.']
                    ]
                ]);
    }

    /**
     * Test user cannot update category while unauthenticated via API.
     * 
     * This test verifies that a user cannot update category while unauthenticated via API endpoint.
     */
    public function test_user_cannot_update_category_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }


        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $newCategoryName = 'Old Category';
        $updatedCategoryName = 'New Category';

        $newCategory = Category::create([
            'name' => $newCategoryName
        ]);

        $response = $this->putJson(route('api.v1.roles.update', $newCategory->id), [
            'name' => $updatedCategoryName
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user can delete category via API.
     * 
     * This test verifies that a user can delete category via API endpoint.
     */
    public function test_user_can_delete_category(): void
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

        $newCategory = Category::create([
            'name' => "Sample Role"
        ]);

        $response = $this->deleteJson(route('api.v1.categories.destroy', $newCategory->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Successfully Category Deleted.', 
                ]);
    }

    /**
     * Test user cannot delete category while category was already associated to user via API.
     * 
     * This test verifies that a user cannot delete category while category was already associated to user via API endpoint.
     */
    public function test_user_cannot_delete_category_while_already_associated_to_subcategory(): void
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
            'name' => 'Category Test'
        ]);

        $categoryId = $category->id;

        Subcategory::create([
            'category_id' => $categoryId,
            'name' => 'Category Test'
        ]);

        $response = $this->deleteJson(route('api.v1.categories.destroy', $categoryId));

        $response->assertCookie('laravel_session')
                ->assertStatus(409)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Cannot delete category. There are subcategories associated with this category.']);
    }

    /**
     * Test user cannot delete category while unauthenticated via API.
     * 
     * This test verifies that a user cannot delete category while unauthenticated via API endpoint.
     */
    public function test_user_cannot_delete_category_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $category = Category::create([
            'name' => 'Category 1'
        ]);

        $response = $this->deleteJson(route('api.v1.categories.destroy', $category));

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user cannot delete category if not existing via API.
     * 
     * This test verifies that a user cannot delete category if not existing via API endpoint.
     */
    public function test_user_cannot_delete_category_if_not_existing(): void
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

        $response = $this->deleteJson(route('api.v1.categories.destroy', 0));

        $response->assertCookie('laravel_session')
                ->assertStatus(404);
    }

    /**
     * Test user can retrieve all categories via API.
     * 
     * This test verifies that a user can retrieve all categories via API endpoint.
     */
    public function test_user_can_retrieve_all_categories(): void
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

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
                ->assertJsonStructure(['data', 'links']);
    }

    /**
     * Test user can retrieve all categories via API.
     * 
     * This test verifies that a user cannot retrieve all categories via API endpoint.
     */
    public function test_user_cannot_retrieve_all_categories_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user can retrieve specific category via API.
     * 
     * This test verifies that a user can retrieve specific category via API endpoint.
     */
    public function test_user_can_retrieve_specific_category(): void
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

        $response = $this->getJson(route('api.v1.categories.show', $category));

        $response->assertStatus(200)
                ->assertJsonStructure(['data']);
    }

    /**
     * Test user cannot retrieve specific category while unauthenticated via API.
     * 
     * This test verifies that a user cannot retrieve specific category while unauthenticated via API endpoint.
     */
    public function test_user_cannot_retrieve_specific_category_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $category = Category::create([
            'name' => 'Test Category'
        ]);

        $response = $this->getJson(route('api.v1.categories.show', $category->id));

        $response->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user can retrieve all dropdown categories via API.
     * 
     * This test verifies that a user can retrieve all dropdown categories via API endpoint.
     */
    public function test_user_can_retrieve_all_dropdown_categories(): void
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

        $response = $this->getJson(route('api.v1.categories.dropdown'));

        $response->assertStatus(200);
    }

    /**
     * Test user cannot retrieve all dropdown categories while unauthenticated via API.
     * 
     * This test verifies that a user cannot retrieve all dropdown categories while unauthenticated via API endpoint.
     */
    public function test_user_cannot_retrieve_all_dropdown_categories_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $response = $this->getJson(route('api.v1.categories.dropdown'));

        $response->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test other user type is unauthorize to get all categories via API.
     * 
     * This test verifies that other user type is unauthorize to get all categories via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_get_all_categories(): void
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

        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        
        $response = $this->getJson(route('api.v1.categories.index'));

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test other user type is unauthorize to get category via API.
     * 
     * This test verifies that other user type is unauthorize to get category via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_get_category(): void
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
            'name' => "Test Category"
        ]);
        
        $response = $this->getJson(route('api.v1.categories.show', $category->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test other user type is unauthorize to store category via API.
     * 
     * This test verifies that other user type is unauthorize to get store via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_store_category(): void
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

        
        $response = $this->postJson(route('api.v1.categories.store'), [
            'name' => "Test Category"
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }
}
