<?php

namespace Tests\Feature\Entity\Role;

use App\Enums\UserType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotEquals;

class RoleTest extends TestCase
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
    }

    /**
     * Test admin can store role with valid inputs via API.
     * 
     * This test verifies that an admin can store role with valid inputs via API endpoint.
     */
    public function test_admin_can_store_role_with_valid_inputs(): void
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

        $response = $this->post('/api/v1/roles', [
            'name' => 'Sample Role'
        ]);

        $response->assertStatus(201)
                ->assertJson(['message' => 'Successfully Role Created.']);
    }

    /**
     * Test admin cannot store role with empty role name inputs via API.
     * 
     * This test verifies that a user cannot store role with empty role name via API endpoint.
     */
    public function test_admin_cannot_store_role_with_empty_role_name(): void
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

        $response = $this->postJson('/api/v1/roles', [
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
     * Test admin cannot store role with role name more than 100 hundred characters via API.
     * 
     * This test verifies that a user cannot store role with role name more than 100 characters via API endpoint.
     */
    public function test_admin_cannot_store_role_with_role_name_more_than_one_hundred_chars(): void
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

        $response = $this->postJson('/api/v1/roles', [
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
     * Test admin cannot store role while unauthenticated via API.
     * 
     * This test verifies that a user cannot store while unauthenticated via API endpoint.
     */
    public function test_admin_cannot_store_role_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $response = $this->postJson('/api/v1/roles', [
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
     * Test admin can update role with valid inputs via API.
     * 
     * This test verifies that an admin can update role with valid inputs via API endpoint.
     */
    public function test_admin_can_update_role_with_valid_inputs(): void
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

        $newRoleName = 'Old Role';
        $updatedRoleName = 'New Role';

        $newRole = Role::create([
            'name' => $newRoleName
        ]);

        $response = $this->putJson(route('api.v1.roles.update', $newRole->id), [
            'name' => $updatedRoleName
        ]);

        $updatedRole = Role::find($newRole->id);

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJson(['message' => 'Successfully Role Updated.']);

        $this->assertNotEquals($newRoleName, $updatedRole->name, 'Values should not be equals');
    }

    /**
     * Test admin cannot update role with empty role name inputs via API.
     * 
     * This test verifies that a user cannot update role with empty role name via API endpoint.
     */
    public function test_admin_cannot_update_role_with_empty_fields(): void
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

        $newRoleName = 'Old Role';

        $newRole = Role::create([
            'name' => $newRoleName
        ]);

        $response = $this->putJson(route('api.v1.roles.update', $newRole->id), [
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
     * Test admin cannot store role with role name more than 100 hundred characters via API.
     * 
     * This test verifies that a user cannot store role with role name more than 100 characters via API endpoint.
     */
    public function test_admin_cannot_update_role_with_role_name_more_than_one_hundred_chars(): void
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

        $newRoleName = 'Old Role';

        $newRole = Role::create([
            'name' => $newRoleName
        ]);

        $response = $this->putJson(route('api.v1.roles.update', $newRole->id), [
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
     * Test admin cannot update role while unauthenticated via API.
     * 
     * This test verifies that a user cannot update while unauthenticated via API endpoint.
     */
    public function test_admin_cannot_update_role_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }


        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $newRoleName = 'Old Role';
        $updatedRoleName = 'New Role';

        $newRole = Role::create([
            'name' => $newRoleName
        ]);

        $response = $this->putJson(route('api.v1.roles.update', $newRole->id), [
            'name' => $updatedRoleName
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test admin can delete role via API.
     * 
     * This test verifies that a user can delete role via API endpoint.
     */
    public function test_admin_can_delete_role(): void
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

        $newRole = Role::create([
            'name' => "Sample Role"
        ]);

        $response = $this->deleteJson(route('api.v1.roles.destroy', $newRole->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Successfully Role Deleted.', 
                ]);
    }

    /**
     * Test admin cannot delete role while role was already associated to user via API.
     * 
     * This test verifies that a user cannot delete role while role was already associated to user via API endpoint.
     */
    public function test_admin_cannot_delete_role_while_already_associated_to_user(): void
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

        $response = $this->deleteJson(route('api.v1.roles.destroy', $role->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(409)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Cannot delete role. There are users associated with this role.']);
    }

    /**
     * Test admin cannot delete role while unauthenticated via API.
     * 
     * This test verifies that a user cannot delete role while unauthenticated via API endpoint.
     */
    public function test_admin_cannot_delete_role_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $response = $this->deleteJson(route('api.v1.roles.destroy', $role->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test admin cannot delete role if not existing via API.
     * 
     * This test verifies that a user cannot delete role if not existing via API endpoint.
     */
    public function test_admin_cannot_delete_role_if_not_existing(): void
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

        $response = $this->deleteJson(route('api.v1.roles.destroy', 0));

        $response->assertCookie('laravel_session')
                ->assertStatus(404);
    }

    /**
     * Test admin can retrieve all roles via API.
     * 
     * This test verifies that an admin can retrieve all roles via API endpoint.
     */
    public function test_admin_can_retrieve_all_roles(): void
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

        // Log in the user
        $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $response = $this->getJson('/api/v1/roles');

        $response->assertStatus(200)
                ->assertJsonStructure(['data', 'links']);
    }

    /**
     * Test admin can retrieve all roles via API.
     * 
     * This test verifies that a user can retrieve all roles via API endpoint.
     */
    public function test_admin_cannot_retrieve_all_roles_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $response = $this->getJson('/api/v1/roles');

        $response->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test admin can retrieve specific role via API.
     * 
     * This test verifies that a user can retrieve specific role via API endpoint.
     */
    public function test_admin_can_retrieve_specific_role(): void
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

        $response = $this->getJson(route('api.v1.roles.show', 1));

        $response->assertStatus(200)
                ->assertJsonStructure(['data']);
    }

    /**
     * Test admin can retrieve specific role via API.
     * 
     * This test verifies that a user can retrieve specific role via API endpoint.
     */
    public function test_admin_cannot_retrieve_specific_role_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $response = $this->getJson('/api/v1/roles/1');

        $response->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test admin can retrieve all dropdown roles via API.
     * 
     * This test verifies that a user can retrieve all dropdown roles via API endpoint.
     */
    public function test_admin_can_retrieve_all_dropdown_roles(): void
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

        $response = $this->getJson(route('api.v1.roles.dropdown'));

        $response->assertStatus(200);
    }

    /**
     * Test admin cannot retrieve all dropdown roles while unauthenticated via API.
     * 
     * This test verifies that a user cannot retrieve all dropdown roles while unauthenticated via API endpoint.
     */
    public function test_admin_cannot_retrieve_all_dropdown_roles_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $response = $this->getJson(route('api.v1.roles.dropdown'));

        $response->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test other user type is unauthorize to get all roles via API.
     * 
     * This test verifies that other user type is unauthorize to get all roles via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_get_all_roles(): void
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
        
        $response = $this->getJson(route('api.v1.roles.index'));

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test other user type is unauthorize to get role via API.
     * 
     * This test verifies that other user type is unauthorize to get role via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_get_role(): void
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

        $newRole = Role::create([
            'name' => 'Test Role'
        ]);
        
        $response = $this->getJson(route('api.v1.roles.show', $newRole->id));

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test other user type is unauthorize to store role via API.
     * 
     * This test verifies that other user type is unauthorize to store role via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_store_role(): void
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
        
        $response = $this->postJson(route('api.v1.roles.store'), [
            'name' => 'Test Role'
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test other user type is unauthorize to update role via API.
     * 
     * This test verifies that other user type is unauthorize to update role via API endpoint.
     */
    public function test_other_user_type_is_unauthorize_to_update_role(): void
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

        $newRole = Role::create([
            'name' => 'Test Role'
        ]);
        
        $response = $this->putJson(route('api.v1.roles.update', $newRole->id), [
            'name' => 'Test Role'
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }
}
