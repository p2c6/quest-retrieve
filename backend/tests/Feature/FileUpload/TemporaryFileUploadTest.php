<?php

namespace Tests\Feature\FileUpload;

use App\Enums\UserType;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TemporaryFileUploadTest extends TestCase
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
     * Test user can upload temporary file with valid input via API.
     * 
     * This test verifies that a user can upload temporary file with valid input via API endpoint.
     */
    public function test_user_can_upload_temporary_file_with_valid_input(): void
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

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $file = UploadedFile::fake()->image('example.jpg');

        $fileExtension = $file->getClientOriginalExtension();

        $response = $this->postJson(route('api.v1.temporary-file.upload'),  [
            'file' => $file
        ]);

        $filePath = $response->json()['file_path'];

        $this->assertDatabaseHas('temporary_files', ['file_name' => $filePath .'.'. $fileExtension]);

        $response->assertStatus(200)
                    ->assertJsonStructure(['file_path']);
    }

    /**
     * Test user cannot upload temporary file with empty field via API.
     * 
     * This test verifies that a user cannot upload temporary file with empty field  via API endpoint.
     */
    public function test_user_cannot_upload_temporary_file_with_empty_field(): void
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

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $response = $this->postJson(route('api.v1.temporary-file.upload'),  [
            'file' => ''
        ]);

        $response->assertStatus(422)
                    ->assertJsonStructure(['message', 'errors'])
                    ->assertJson(['errors' => [
                        'file' => ['The file field is required.']
                    ]])
                    ->assertJsonValidationErrors(['file']);
    }

    /**
     * Test user cannot upload temporary file if not image field via API.
     * 
     * This test verifies that a user cannot upload temporary file if not image via API endpoint.
     */
    public function test_user_cannot_upload_temporary_file_if_not_image(): void
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

        $response = $this->postJson('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        $file = UploadedFile::fake()->create('example.pdf', 100);

        $response = $this->postJson(route('api.v1.temporary-file.upload'),  [
            'file' => $file
        ]);

        $response->assertStatus(422)
                    ->assertJsonStructure(['message', 'errors'])
                    ->assertJson(['errors' => [
                        'file' => ['The file field must be a file of type: jpg, jpeg, png.']
                    ]])
                    ->assertJsonValidationErrors(['file']);
    }
}
