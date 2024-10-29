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
     * Test user can register with valid inputs successfully via API.
     * 
     * This test verifies that a user can register with valid inputs successfully via API endpoint.
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

        $response = $this->putJson(route('api.v1.profile.update', $user->id), [
            'last_name' => 'Does',
            'first_name' => 'Jim',
            'birthday' => '2024-05-06',
            'contact_no' => '09842613'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson([ 'message' => 'Successfully User Profile Updated.']);
    }
}
