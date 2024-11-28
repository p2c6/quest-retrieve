<?php

namespace Tests\Feature\User;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Enums\UserType;
use App\Models\Category;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Subcategory;
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
     * Test admin can get all users via API.
     * 
     * This test verifies that an admin can get all  users via API endpoint.
     */
    public function test_admin_can_get_all_users(): void
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
        
        $response = $this->getJson(route('api.v1.users.index'));

        $response->assertCookie('laravel_session')
                ->assertStatus(200);
    }

    /**
     * Test other user type is unauthorize to get all users via API.
     * 
     * This test verifies that other user type is unauthorize to get all users via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_get_all_users(): void
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
        
        $response = $this->getJson(route('api.v1.users.index'));

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test user cannot get all users while unauthenticated via API.
     * 
     * This test verifies that user cannot get all users while unauthenticated via API endpoint.
     */
    public function test_user_cannot_get_all_users_while_unauthenticated(): void
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
        
        $response = $this->getJson(route('api.v1.users.index'));

        $response->assertCookie('laravel_session')
        ->assertStatus(401)
        ->assertJsonStructure(['message'])
        ->assertJson([
            'message' => 'Unauthenticated.', 
        ]);
    }

    /**
     * Test admin can get user via API.
     * 
     * This test verifies that an admin can get user via API endpoint.
     */
    public function test_admin_can_get_user(): void
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
        
        $response = $this->getJson(route('api.v1.users.show', $user->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(200);
    }

    /**
     * Test other user type is unauthorize to get user via API.
     * 
     * This test verifies that other user type is unauthorize to get user via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_get_user(): void
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
        
        $response = $this->getJson(route('api.v1.users.show', $user->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
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
     * This test verifies that other user type is unauthorize to store new user via API endpoint.
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

    /**
     * Test user cannot store  new user while unauthenticated via API.
     * 
     * This test verifies that user cannot store  new user while unauthenticated via API endpoint.
     */
    public function test_user_cannot_store_user_while_unauthenticated(): void
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
        
        $response = $this->postJson(route('api.v1.users.store'), [
            'email' => 'test000@gmail.com', 
            'password' => 'password123', 
            'password_confirmation' => 'password123', 
            'role_id' => UserType::PUBLIC_USER
        ]);

        $response->assertCookie('laravel_session')
        ->assertStatus(401)
        ->assertJsonStructure(['message'])
        ->assertJson([
            'message' => 'Unauthenticated.', 
        ]);
    }

    /**
     * Test admin can update user with valid inputs via API.
     * 
     * This test verifies that an admin can update user with valid inputs via API endpoint.
     */
    public function test_admin_can_update_user_with_valid_inputs(): void
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

        $originalEmail = 'test001@gmail.com';
        $originalPassword = 'password1234';
        $originalRole = UserType::MODERATOR;

        $user = User::create([
            'email' => $originalEmail,
            'password' => $originalPassword,
            'role_id' => $originalRole,
        ]);

        $updatedEmail = 'test000@gmail.com';
        $updatedPassword = 'password123';
        $updatedRole = UserType::PUBLIC_USER;
        
        $response = $this->putJson(route('api.v1.users.update', $user->id), [
            'email' => $updatedEmail, 
            'password' => $updatedPassword, 
            'password_confirmation' => $updatedPassword, 
            'role_id' => $updatedRole
        ]);

        $this->assertNotSame($originalEmail, $updatedEmail, 'Email must not equal to previous');
        $this->assertNotSame($originalPassword, $updatedPassword, 'Password must not equal to previous');
        $this->assertNotSame($originalRole, $updatedRole, 'Role must not equal to previous');

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully User Updated.']);
    }

    /**
     * Test admin cannot update user with all empty fields via API.
     * 
     * This test verifies that an admin cannot update user with all empty fields via API endpoint.
     */
    public function test_admin_cannot_update_user_with_all_empty_fields(): void
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

        $originalEmail = 'test001@gmail.com';
        $originalPassword = 'password1234';
        $originalRole = UserType::MODERATOR;

        $user = User::create([
            'email' => $originalEmail,
            'password' => $originalPassword,
            'role_id' => $originalRole,
        ]);

        $updatedEmail = '';
        $updatedPassword = '';
        $updatedRole = '';
        
        $response = $this->putJson(route('api.v1.users.update', $user->id), [
            'email' => $updatedEmail, 
            'password' => $updatedPassword, 
            'password_confirmation' => $updatedPassword, 
            'role_id' => $updatedRole
        ]);

        $this->assertNotSame($originalEmail, $updatedEmail, 'Email must not equal to previous');
        $this->assertNotSame($originalPassword, $updatedPassword, 'Password must not equal to previous');
        $this->assertNotSame($originalRole, $updatedRole, 'Role must not equal to previous');

        $response->assertCookie('laravel_session')
                ->assertStatus(422)
                ->assertJsonValidationErrors([
                'email',
                'password',
                'role_id',
        ]);
    }

    /**
     * Test other user type is unauthorize to update new user via API.
     * 
     * This test verifies that other user type is unauthorize to update new user via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_update_user(): void
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
        
        
        $originalEmail = 'test001@gmail.com';
        $originalPassword = 'password1234';
        $originalRole = UserType::MODERATOR;

        $user = User::create([
            'email' => $originalEmail,
            'password' => $originalPassword,
            'role_id' => $originalRole,
        ]);

        $updatedEmail = 'test000@gmail.com';
        $updatedPassword = 'password123';
        $updatedRole = UserType::PUBLIC_USER;
        
        $response = $this->putJson(route('api.v1.users.update', $user->id), [
            'email' => $updatedEmail, 
            'password' => $updatedPassword, 
            'password_confirmation' => $updatedPassword, 
            'role_id' => $updatedRole
        ]);

        $this->assertNotSame($originalEmail, $updatedEmail, 'Email must not equal to previous');
        $this->assertNotSame($originalPassword, $updatedPassword, 'Password must not equal to previous');
        $this->assertNotSame($originalRole, $updatedRole, 'Role must not equal to previous');

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test user cannot update user while unauthenticated inputs via API.
     * 
     * This test verifies that a user cannot update user while unauthenticated via API endpoint.
     */
    public function test_user_cannot_update_user_while_unauthenticated(): void
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

        $originalEmail = 'test001@gmail.com';
        $originalPassword = 'password1234';
        $originalRole = UserType::MODERATOR;

        $user = User::create([
            'email' => $originalEmail,
            'password' => $originalPassword,
            'role_id' => $originalRole,
        ]);

        $updatedEmail = 'test000@gmail.com';
        $updatedPassword = 'password123';
        $updatedRole = UserType::PUBLIC_USER;
        
        $response = $this->putJson(route('api.v1.users.update', $user->id), [
            'email' => $updatedEmail, 
            'password' => $updatedPassword, 
            'password_confirmation' => $updatedPassword, 
            'role_id' => $updatedRole
        ]);

        $this->assertNotSame($originalEmail, $updatedEmail, 'Email must not equal to previous');
        $this->assertNotSame($originalPassword, $updatedPassword, 'Password must not equal to previous');
        $this->assertNotSame($originalRole, $updatedRole, 'Role must not equal to previous');

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }
    
    /**
     * Test admin can delete user via API.
     * 
     * This test verifies that an admin can delete user via API endpoint.
     */
    public function test_admin_can_delete_user(): void
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

        $newUser = User::create([
            'email' => 'test000@gmail.com', 
            'password' => 'password123', 
            'password_confirmation' => 'password123', 
            'role_id' => UserType::PUBLIC_USER
        ]);
        
        $response = $this->deleteJson(route('api.v1.users.destroy', $newUser->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully User Deleted.']);
    }

    /**
     * Test admin cannot delete while user already associated to post via API.
     * 
     * This test verifies that an admin cannot delete user while user already associated to post via API endpoint.
     */
    public function test_admin_cannot_delete_user_while_already_associated_to_post(): void
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
            'status' => PostStatus::PENDING,
        ]);

        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson(route('api.v1.users.destroy', $user->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(409)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Cannot delete user. There are posts associated with this user.'
                ]);
    }
    
    /**
     * Test other user type is unauthorize to delete user via API.
     * 
     * This test verifies that other user type is unauthorize to delete user via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_delete_user(): void
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

        $newUser = User::create([
            'email' => 'test000@gmail.com', 
            'password' => 'password123', 
            'password_confirmation' => 'password123', 
            'role_id' => UserType::PUBLIC_USER
        ]);
        
        $response = $this->deleteJson(route('api.v1.users.destroy', $newUser->id));

        $response->assertCookie('laravel_session')
                    ->assertStatus(403)
                    ->assertJsonStructure(['message'])
                    ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test user cannot delete user while unauthenticated via API.
     * 
     * This test verifies that user cannot delete user while unauthenticated via API endpoint.
     */
    public function test_user_cannot_delete_user_while_unauthenticated(): void
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

        $newUser = User::create([
            'email' => 'test000@gmail.com', 
            'password' => 'password123', 
            'password_confirmation' => 'password123', 
            'role_id' => UserType::PUBLIC_USER
        ]);
        
        $response = $this->deleteJson(route('api.v1.users.destroy', $newUser->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }
}
