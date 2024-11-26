<?php

namespace Tests\Feature\PostApproval;

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

class PostApprovalTest extends TestCase
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
     * Test admin can approve post via API.
     * 
     * This test verifies that an admin can approve post via API endpoint.
     */
    public function test_admin_can_approve_post(): void
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

        $post = Post::where('status', PostStatus::PENDING)->first();

        $updatedStatus = PostStatus::ON_PROCESSING;
        
        $response = $this->putJson(route('api.v1.for-approval.approve', $post->id), [
            'status' => $updatedStatus
        ]);

        $this->assertNotEquals($post->status, $updatedStatus);

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully Post Approved']);
    }

    /**
     * Test moderator can approve post via API.
     * 
     * This test verifies that a moderator can approve post via API endpoint.
     */
    public function test_moderator_can_approve_post(): void
    {
        $role = Role::where('id', UserType::MODERATOR)->first();

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

        $post = Post::where('status', PostStatus::PENDING)->first();

        $updatedStatus = PostStatus::ON_PROCESSING;
        
        $response = $this->putJson(route('api.v1.for-approval.approve', $post->id), [
            'status' => $updatedStatus
        ]);

        $this->assertNotEquals($post->status, $updatedStatus);

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully Post Approved']);
    }

    /**
     * Test user cannot approve post while unauthenticated via API.
     * 
     * This test verifies that a user cannot approve post while unauthenticated via API endpoint.
     */
    public function test_user_cannot_approve_post_while_unauthenticated(): void
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

        $post = Post::where('status', PostStatus::PENDING)->first();

        $updatedStatus = PostStatus::ON_PROCESSING;
        
        $response = $this->putJson(route('api.v1.for-approval.approve', $post->id), [
            'status' => $updatedStatus
        ]);

        $this->assertNotEquals($post->status, $updatedStatus);

        $response->assertCookie('laravel_session')
        ->assertStatus(401)
        ->assertJsonStructure(['message'])
        ->assertJson([
            'message' => 'Unauthenticated.', 
        ]);
    }

    /**
     * Test admin can reject post via API.
     * 
     * This test verifies that an admin can reject post via API endpoint.
     */
    public function test_admin_can_reject_post(): void
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

        $post = Post::where('status', PostStatus::PENDING)->first();

        $updatedStatus = PostStatus::REJECT;
        
        $response = $this->putJson(route('api.v1.for-approval.reject', $post->id), [
            'status' => $updatedStatus
        ]);

        $this->assertNotEquals($post->status, $updatedStatus);

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully Post Rejected']);
    }

    /**
     * Test moderator can reject post via API.
     * 
     * This test verifies that a moderator can reject post via API endpoint.
     */
    public function test_moderator_can_reject_post(): void
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

        $post = Post::where('status', PostStatus::PENDING)->first();

        $updatedStatus = PostStatus::REJECT;
        
        $response = $this->putJson(route('api.v1.for-approval.reject', $post->id), [
            'status' => $updatedStatus
        ]);

        $this->assertNotEquals($post->status, $updatedStatus);

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully Post Rejected']);
    }

    /**
     * Test user cannot reject post while unauthenticated via API.
     * 
     * This test verifies that a user cannot reject post while unauthenticated via API endpoint.
     */
    public function test_user_cannot_reject_post_while_unauthenticated(): void
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

        $post = Post::where('status', PostStatus::PENDING)->first();

        $updatedStatus = PostStatus::REJECT;
        
        $response = $this->putJson(route('api.v1.for-approval.reject', $post->id), [
            'status' => $updatedStatus
        ]);

        $this->assertNotEquals($post->status, $updatedStatus);

        $response->assertCookie('laravel_session')
        ->assertStatus(401)
        ->assertJsonStructure(['message'])
        ->assertJson([
            'message' => 'Unauthenticated.', 
        ]);
    }

    /**
     * Test public user cannot approve post via API.
     * 
     * This test verifies that a public user cannot approve post via API endpoint.
     */
    public function test_public_user_cannot_access_post_approval_list(): void
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

        $post = Post::where('status', PostStatus::PENDING)->first();

        $updatedStatus = PostStatus::ON_PROCESSING;
        
        $response = $this->putJson(route('api.v1.for-approval.approve', $post->id), [
            'status' => $updatedStatus
        ]);

        $this->assertNotEquals($post->status, $updatedStatus);

        $response->assertCookie('laravel_session')
                ->assertStatus(403);
    }
}
