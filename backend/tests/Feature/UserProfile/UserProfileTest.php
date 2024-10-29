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
}
