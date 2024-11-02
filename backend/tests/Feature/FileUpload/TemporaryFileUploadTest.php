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
use Illuminate\Support\Facades\Storage;
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

        $temporaryUuidName = $response->json()['file_path'];
        
        $modifiedFile = "$temporaryUuidName.$fileExtension";

        $this->assertTrue(Storage::disk('public')
            ->exists('uploads/temporary/'.$temporaryUuidName.'/'.$modifiedFile), "The file does not exist in public storage");

        $fullPath = public_path('storage/uploads/temporary/'.$temporaryUuidName.'/'.$modifiedFile);
        $this->assertFileExists($fullPath, 'The file not found at the public storage.');

        $this->assertDatabaseHas('temporary_files', ['file_name' => $modifiedFile ]);

        $response->assertCookie('laravel_session')
                    ->assertStatus(200)
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

        $response->assertCookie('laravel_session')
                    ->assertStatus(422)
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

        $response->assertCookie('laravel_session')
                    ->assertStatus(422)
                    ->assertJsonStructure(['message', 'errors'])
                    ->assertJson(['errors' => [
                        'file' => ['The file field must be a file of type: jpg, jpeg, png.']
                    ]])
                    ->assertJsonValidationErrors(['file']);
    }

    /**
     * Test user cannot upload temporary file while unauthenticated via API.
     * 
     * This test verifies that a user cannot upload temporary file while unauthenticated via API endpoint.
     */
    public function test_user_cannot_upload_temporary_file_while_unauthenticated(): void
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

        $file = UploadedFile::fake()->image('example.jpg');

        $response = $this->postJson(route('api.v1.temporary-file.upload'),  [
            'file' => $file
        ]);

        $response->assertCookie('laravel_session')
                    ->assertStatus(401)
                    ->assertJsonStructure(['message'])
                    ->assertJson(['message' => 'Unauthenticated.']);
    }

    /**
     * Test user can revert temporary file with valid input via API.
     * 
     * This test verifies that a user can revert temporary file with valid input via API endpoint.
     */
    public function test_user_can_revert_temporary_file_with_valid_input(): void
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

        $temporaryUuidFolderName = $response->json()['file_path'];
        
        $modifiedFile = "$temporaryUuidFolderName.$fileExtension";


        $this->assertDatabaseHas('temporary_files', ['file_name' => $modifiedFile]);

        $response = $this->postJson(route('api.v1.temporary-file.revert'),  [
            'file_path' => $temporaryUuidFolderName
        ]);

        $this->assertTrue(!Storage::disk('public')
            ->exists('uploads/temporary/'.$temporaryUuidFolderName.'/'.$modifiedFile), "The file exist in public storage");

        $fullPath = public_path('storage/uploads/temporary/'.$temporaryUuidFolderName.'/'.$modifiedFile);
        $this->assertFileDoesNotExist($fullPath, 'The file found at the public storage.');

        $this->assertDatabaseMissing('temporary_files', ['file_name' => $modifiedFile]);

        $response->assertCookie('laravel_session')
                    ->assertStatus(200)
                    ->assertJsonStructure(['message'])
                    ->assertJson(['message' => 'Directory And File Removed Successfully.']);
    }

    /**
     * Test user cannot revert temporary file with empty field via API.
     * 
     * This test verifies that a user cannot revert temporary file with empty field via API endpoint.
     */
    public function test_user_cannot_revert_temporary_file_with_empty_field(): void
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

        $fileName = "$filePath.$fileExtension";

        $this->assertDatabaseHas('temporary_files', ['file_name' => $fileName]);

        $response = $this->postJson(route('api.v1.temporary-file.revert'),  [
            'file_path' => ''
        ]);

        $response->assertCookie('laravel_session')
                    ->assertStatus(422)
                    ->assertJsonStructure(['message', 'errors'])
                    ->assertJson(['message' => 'The file path field is required.' ,
                    'errors' => [
                        'file_path' => ['The file path field is required.']
                    ]])
                    ->assertJsonValidationErrors(['file_path']);
    }

    /**
     * Test user cannot revert temporary file if not a string file name via API.
     * 
     * This test verifies that a user cannot revert temporary file if not a string file name via API endpoint.
     */
    public function test_user_cannot_revert_temporary_file_if_not_string_file_name(): void
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

        $fileName = 1;


        $response = $this->postJson(route('api.v1.temporary-file.revert'),  [
            'file_path' => $fileName
        ]);

        $this->assertIsNotString($fileName, 'File Path Must be String');

        $response->assertCookie('laravel_session')
                    ->assertStatus(422)
                    ->assertJsonStructure(['message', 'errors'])
                    ->assertJson(['message' => 'The file path field must be a string.' ,
                    'errors' => [
                        'file_path' => ['The file path field must be a string.']
                    ]])
                    ->assertJsonValidationErrors(['file_path']);
    }

    /**
     * Test user cannot revert temporary file while unauthenticated via API.
     * 
     * This test verifies that a user cannot revert temporary file while unauthenticated via API endpoint.
     */
    public function test_user_cannot_revert_temporary_file_while_unauthenticated(): void
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

        $file = UploadedFile::fake()->image('example.jpg');

        $fileExtension = $file->getClientOriginalExtension();

        $response = $this->postJson(route('api.v1.temporary-file.upload'),  [
            'file' => $file
        ]);

        $response->assertCookie('laravel_session')
                    ->assertStatus(401)
                    ->assertJsonStructure(['message'])
                    ->assertJson(['message' => 'Unauthenticated.']);
    }
}
