<?php

namespace Tests\Feature\UserProfile;

use App\Enums\UserType;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserProfileTest extends TestCase
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
     * Test user can update user profile with valid inputs successfully via API.
     * 
     * This test verifies that a user can update user profile with valid inputs successfully via API endpoint.
     */
    public function test_user_can_update_user_profile_with_valid_inputs(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $updatedLastName = "Does";
        $updatedFirstName = "Jim";
        $updatedBirthday = "2024-05-06";
        $updatedContactNo = "09842613";

        $response = $this->putJson(route('api.v1.profile.update', $user->id), [
            'last_name' => $updatedLastName,
            'first_name' => $updatedFirstName,
            'birthday' => $updatedBirthday,
            'contact_no' => $updatedContactNo
        ]);

        $this->assertNotSame('Doe', $updatedLastName, 'Last Name must not equal to previous');
        $this->assertNotSame('Rick', $updatedFirstName, 'First Name must not equal to previous');
        $this->assertNotSame('2024-05-19', $updatedBirthday, 'Birthday must not equal to previous');
        $this->assertNotSame('12345', $updatedContactNo, 'Contact Number must not equal to previous');

        $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson([ 'message' => 'Successfully User Profile Updated.']);
    }

    
    /**
     * Test user cannot update user profile with all fields are empty via API.
     * 
     * This test verifies that a user cannot update user profile with all feilds are empty via API endpoint.
     */
    public function test_user_cannot_update_user_profile_with_all_fields_are_empty(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $response = $this->putJson(route('api.v1.profile.update', $user->id), [
            'last_name' => "",
            'first_name' => "",
            'birthday' => "",
            'contact_no' => ""
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'last_name',
            'first_name',
            'birthday',
            'contact_no',
        ]);
    }

    /**
     * Test user cannot update user profile while unauthenticated via API.
     * 
     * This test verifies that a user cannot update user profile while unauthenticated via API endpoint.
     */
    public function test_user_cannot_update_user_profile_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $updatedLastName = "Does";
        $updatedFirstName = "Jim";
        $updatedBirthday = "2024-05-06";
        $updatedContactNo = "09842613";

        $response = $this->putJson(route('api.v1.profile.update', $user->id), [
            'last_name' => $updatedLastName,
            'first_name' => $updatedFirstName,
            'birthday' => $updatedBirthday,
            'contact_no' => $updatedContactNo
        ]);

        $this->assertNotSame('Doe', $updatedLastName, 'Last Name must not equal to previous');
        $this->assertNotSame('Rick', $updatedFirstName, 'First Name must not equal to previous');
        $this->assertNotSame('2024-05-19', $updatedBirthday, 'Birthday must not equal to previous');
        $this->assertNotSame('12345', $updatedContactNo, 'Contact Number must not equal to previous');

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }

    /**
     * Test user can update password via user profile with valid inputs successfully via API.
     * 
     * This test verifies that a user can update password via user profile with valid inputs successfully via API endpoint.
     */
    public function test_user_can_update_password_via_user_profile_with_valid_inputs(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $currentPassword = "password123";
        $password = "password1234";
        $passwordConfirmation = "password1234";


        $response = $this->putJson(route('api.v1.profile.password.update', $user->id), [
            'current_password' => $currentPassword,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ]);

        $this->assertNotSame($currentPassword, $password, 'New Password must not equal to previous');

        $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson([ 'message' => 'Successfully Password Updated.']);
    }

    /**
     * Test user cannot update password via user profile with all fields are empty via API.
     * 
     * This test verifies that a user cannot update password via user profile with all feilds are empty via API endpoint.
     */
    public function test_user_cannot_update_password_via_user_profile_with_all_fields_are_empty(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $response = $this->putJson(route('api.v1.profile.password.update', $user->id), [
            'current_password' => "",
            'password' => "",
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'current_password',
            'password',
        ]);
    }

    /**
     * Test user cannot update password via user profile with wrong current password via API.
     * 
     * This test verifies that a user cannot update password via user profile with wrong current password via API endpoint.
     */
    public function test_user_cannot_update_password_via_user_profile_with_wrong_current_password(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $response = $this->putJson(route('api.v1.profile.password.update', $user->id), [
            'current_password' => "password111",
            'password' => "password1234561",
            'password_confirmation' => 'password1234561'
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'current_password',
        ]);
    }

    /**
     * Test user cannot update password via user profile if password confirmation not match via API.
     * 
     * This test verifies that a user cannot update password via user profile if password confirmation not match via API endpoint.
     */
    public function test_user_cannot_update_password_via_user_profile_if_confirmation_not_match(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $response = $this->putJson(route('api.v1.profile.password.update', $user->id), [
            'current_password' => "password111",
            'password' => "password123456",
            'password_confirmation' => 'password1234561'
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'password',
        ]);
    }

    /**
     * Test user cannot update password via user profile password while unauthenticated via API.
     * 
     * This test verifies that a user cannot update password via user profile while unauthenticated via API endpoint.
     */
    public function test_user_cannot_update_password_via_user_profile_while_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');

        $csrf->assertCookie('XSRF-TOKEN');

        $password = "jimmy123456789";
        $passwordConfirmation = "jimmy123456789";
        $currentPassword = "password123";

        $response = $this->putJson(route('api.v1.profile.password.update', $user->id), [
            'current_password' => $currentPassword,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(401)
                ->assertJsonStructure(['message'])
                ->assertJson([
                    'message' => 'Unauthenticated.', 
                ]);
    }
}
