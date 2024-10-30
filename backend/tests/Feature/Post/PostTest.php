<?php

namespace Tests\Feature\Post;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Enums\UserType;
use App\Models\Category;
use App\Models\Post;
use App\Models\Role;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
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
     * Test user can post with valid inputs via API.
     * 
     * This test verifies that a user can post with valid inputs via API endpoint.
     */
    public function test_user_can_store_post_with_valid_inputs(): void
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
        
        $response = $this->postJson(route('api.v1.posts.store'), [
            'user_id' => $user->id,
            'type' => PostType::LOST,
            'subcategory_id' => $subCategoryId,
            'incident_location' => 'Quezon City',
            'incident_date' => '2024-05-06',
            'finish_transaction_date' => '2024-05-07',
            'expiration_date' =>  '2024-06-06',
            'status' => PostStatus::PENDING,
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(201)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully Post Created.']);
    }

    /**
     * Test user cannot post with all empty fields via API.
     * 
     * This test verifies that a user cannot post with all empty fields via API endpoint.
     */
    public function test_user_can_store_post_with_all_empty_fields(): void
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
        
        $response = $this->postJson(route('api.v1.posts.store'), [
            'user_id' => $user->id,
            'type' => "",
            'subcategory_id' => "",
            'incident_location' => "",
            'incident_date' => "",
            'finish_transaction_date' => "",
            'expiration_date' =>  "",
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(422)
                ->assertJsonValidationErrors([
                'subcategory_id',
                'type',
                'incident_location',
                'incident_date',
            ]);
    }

    /**
     * Test user cannot store post while unauthenticated via API.
     * 
     * This test verifies that a user cannot store post while unauthenticated via API endpoint.
     */
    public function test_user_cannot_store_post_while_unauthenticated(): void
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

        $category = Category::create([
            'name' => "Sample Category",
        ]);

        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory",
        ]);

        $subCategoryId = $subCategory->id;
        
        $response = $this->postJson(route('api.v1.posts.store'), [
            'user_id' => $user->id,
            'type' => PostType::LOST,
            'subcategory_id' => $subCategoryId,
            'incident_location' => 'Quezon City',
            'incident_date' => '2024-05-06',
            'finish_transaction_date' => '2024-05-07',
            'expiration_date' =>  '2024-06-06',
            'status' => PostStatus::PENDING,
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user can update post with valid inputs via API.
     * 
     * This test verifies that a user can update post with valid inputs via API endpoint.
     */
    public function test_user_can_update_post_with_valid_inputs(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $this->postJson('/api/v1/authentication/login', [
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

        $originalType = "Lost";
        $originalSubCategoryId = $subCategory->id;
        $originalIncidentLocation = 'Manila City';
        $originalIncidentDate = '2024-01-02';

        $post = Post::create([
            'user_id' => $user->id,
            'type' => $originalType,
            'subcategory_id' => $originalSubCategoryId,
            'incident_location' => $originalIncidentLocation,
            'incident_date' => $originalIncidentDate,
            'status' => PostStatus::PENDING
        ]);

        $updatedType = "Found";
        $updatedSubCategoryId = 2;
        $updatedIncidentLocation = 'Quezon City';
        $updatedIncidentDate = '2024-05-02';

        $response = $this->putJson(route('api.v1.posts.update', $post->id), [
            'user_id' => $user->id,
            'type' => $updatedType,
            'subcategory_id' => $updatedSubCategoryId,
            'incident_location' => $updatedIncidentLocation,
            'incident_date' => $updatedIncidentDate,
        ]);

        $this->assertNotSame($originalType, $updatedType, 'Type must not equal to previous');
        $this->assertNotSame($originalSubCategoryId, $updatedSubCategoryId, 'Subcategory must not equal to previous');
        $this->assertNotSame($originalIncidentLocation, $updatedIncidentLocation, 'Incident Location must not equal to previous');
        $this->assertNotSame($originalIncidentDate, $updatedIncidentDate, 'Incident Date must not equal to previous');

        $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson([ 'message' => 'Successfully Post Updated.']);
    }

    /**
     * Test user cannot update post with all empty fields via API.
     * 
     * This test verifies that a user cannot update post with all empty fields via API endpoint.
     */
    public function test_user_cannot_update_post_with_all_empty_fields(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $this->postJson('/api/v1/authentication/login', [
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

        $originalType = "Lost";
        $originalSubCategoryId = $subCategory->id;
        $originalIncidentLocation = 'Manila City';
        $originalIncidentDate = '2024-01-02';

        $post = Post::create([
            'user_id' => $user->id,
            'type' => $originalType,
            'subcategory_id' => $originalSubCategoryId,
            'incident_location' => $originalIncidentLocation,
            'incident_date' => $originalIncidentDate,
            'status' => PostStatus::PENDING
        ]);

        $response = $this->putJson(route('api.v1.posts.update', $post->id), [
            'user_id' => $user->id,
            'type' => "",
            'subcategory_id' => "",
            'incident_location' => "",
            'incident_date' => "",
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(422)
                ->assertJsonValidationErrors([
                'subcategory_id',
                'type',
                'incident_location',
                'incident_date',
            ]);
    }

    /**
     * Test user can update post with valid inputs via API.
     * 
     * This test verifies that a user can update post with valid inputs via API endpoint.
     */
    public function test_user_cannot_update_post_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $category = Category::create([
            'name' => "Sample Category",
        ]);

        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory",
        ]);

        $originalType = "Lost";
        $originalSubCategoryId = $subCategory->id;
        $originalIncidentLocation = 'Manila City';
        $originalIncidentDate = '2024-01-02';

        $post = Post::create([
            'user_id' => $user->id,
            'type' => $originalType,
            'subcategory_id' => $originalSubCategoryId,
            'incident_location' => $originalIncidentLocation,
            'incident_date' => $originalIncidentDate,
            'status' => PostStatus::PENDING
        ]);

        $updatedType = "Found";
        $updatedSubCategoryId = 2;
        $updatedIncidentLocation = 'Quezon City';
        $updatedIncidentDate = '2024-05-02';

        $response = $this->putJson(route('api.v1.posts.update', $post->id), [
            'user_id' => $user->id,
            'type' => $updatedType,
            'subcategory_id' => $updatedSubCategoryId,
            'incident_location' => $updatedIncidentLocation,
            'incident_date' => $updatedIncidentDate,
        ]);

        $this->assertNotSame($originalType, $updatedType, 'Type must not equal to previous');
        $this->assertNotSame($originalSubCategoryId, $updatedSubCategoryId, 'Subcategory must not equal to previous');
        $this->assertNotSame($originalIncidentLocation, $updatedIncidentLocation, 'Incident Location must not equal to previous');
        $this->assertNotSame($originalIncidentDate, $updatedIncidentDate, 'Incident Date must not equal to previous');

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user can post subcategory via API.
     * 
     * This test verifies that a user can post subcategory via API endpoint.
     */
    public function test_user_can_delete_post(): void
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

        
        $originalType = "Lost";
        $originalSubCategoryId = $subCategory->id;
        $originalIncidentLocation = 'Manila City';
        $originalIncidentDate = '2024-01-02';

        $post = Post::create([
            'user_id' => $user->id,
            'type' => $originalType,
            'subcategory_id' => $originalSubCategoryId,
            'incident_location' => $originalIncidentLocation,
            'incident_date' => $originalIncidentDate,
            'status' => PostStatus::PENDING
        ]);
        
        $response = $this->deleteJson(route('api.v1.posts.destroy', $post->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Successfully Post Deleted.', 
                ]);
    }
}
