<?php

namespace Tests\Feature\Public\Post;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Enums\UserType;
use App\Mail\ClaimRequested;
use App\Mail\PostCreated;
use App\Mail\PostDeactivated;
use App\Mail\ReturnRequested;
use App\Models\Category;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Role;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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
     * Test guest user can get all post via API.
     * 
     * This test verifies that a guest user can get all post via API endpoint.
     */
    public function test_guest_user_can_get_all_post(): void
    {
        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $response = $this->getJson(route('api.v1.public.index'));

        $response->assertStatus(200)
                ->assertJsonStructure(['data', 'links']);
    }

    /**
     * Test guest user can claim post even unauthenticated via API.
     * 
     * This test verifies that a guest user can claim post even unauthenticated via API endpoint.
     */
    public function test_guest_user_can_claim_post_even_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

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

        $category = Category::create(['name' => "Sample Category"]);
        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory"
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'type' => "Lost",
            'subcategory_id' => $subCategory->id,
            'incident_location' => 'Manila City',
            'incident_date' => '2024-01-02',
            'status' => PostStatus::ON_PROCESSING
        ]);

        $guestClaimerEmail = 'test555@gmail.com';

        Mail::fake();

        $response = $this->postJson(route('api.v1.public.claim', $post->id), [
            'email' => $guestClaimerEmail,
            'item_description' => "Testing Description",
            'where' => "Quezon City",
            'when' => "December 5, 2024",
            'message' => "Test Message",
            'full_name' => "John Doe",
        ]);

        Mail::assertSent(ClaimRequested::class, function (ClaimRequested $mail) use ($user, $guestClaimerEmail) {
            return $mail->hasSubject('QuestRetrieve - Someone wants to claim your found item') &&
                $mail->hasFrom($guestClaimerEmail) && 
                $mail->hasReplyTo($user->email);
        });

        $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully Claim Requested.']);
    }

    /**
     * Test user can claim post while authenticated via API.
     * 
     * This test verifies that a user can claim post while authenticated via API endpoint.
     */
    public function test_user_can_claim_post_even_authenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        
        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');
        $csrf->assertCookie('XSRF-TOKEN');

        $category = Category::create(['name' => "Sample Category"]);
        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory"
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'type' => "Lost",
            'subcategory_id' => $subCategory->id,
            'incident_location' => 'Manila City',
            'incident_date' => '2024-01-02',
            'status' => PostStatus::ON_PROCESSING
        ]);

        $guestClaimerEmail = 'test555@gmail.com';

        Mail::fake();

        $response = $this->postJson(route('api.v1.public.claim', $post->id), [
            'email' => $guestClaimerEmail,
            'item_description' => "Testing Description",
            'where' => "Quezon City",
            'when' => "December 5, 2024",
            'message' => "Test Message",
            'full_name' => "John Doe",
        ]);

        Mail::assertSent(ClaimRequested::class, function (ClaimRequested $mail) use ($user, $guestClaimerEmail) {
            return $mail->hasSubject('QuestRetrieve - Someone wants to claim your found item') &&
                $mail->hasFrom($guestClaimerEmail) && 
                $mail->hasReplyTo($user->email);
        });

        $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully Claim Requested.']);
    }

    /**
     * Test not public user cannot claim post via API.
     * 
     * This test verifies that not public user cannot claim post via API endpoint.
     */
    public function test_not_public_user_cannot_claim_post(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        
        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');
        $csrf->assertCookie('XSRF-TOKEN');

        $category = Category::create(['name' => "Sample Category"]);
        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory"
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'type' => "Lost",
            'subcategory_id' => $subCategory->id,
            'incident_location' => 'Manila City',
            'incident_date' => '2024-01-02',
            'status' => PostStatus::ON_PROCESSING
        ]);

        $guestClaimerEmail = 'test555@gmail.com';

        $response = $this->postJson(route('api.v1.public.claim', $post->id), [
            'email' => $guestClaimerEmail,
            'item_description' => "Testing Description",
            'where' => "Quezon City",
            'when' => "December 5, 2024",
            'message' => "Test Message",
            'full_name' => "John Doe",
        ]);


        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }

    /**
     * Test user cannot claim post with empty fields via API.
     * 
     * This test verifies user cannot claim post with empty fields via API endpoint.
     */
    public function test_user_cannot_claim_post_with_empty_fields(): void
    {
        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
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

        $category = Category::create(['name' => "Sample Category"]);
        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory"
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'type' => "Lost",
            'subcategory_id' => $subCategory->id,
            'incident_location' => 'Manila City',
            'incident_date' => '2024-01-02',
            'status' => PostStatus::ON_PROCESSING
        ]);

        $guestClaimerEmail = '';

        $response = $this->postJson(route('api.v1.public.claim', $post->id), [
            'email' => $guestClaimerEmail,
            'item_description' => "",
            'where' => "",
            'when' => "",
            'message' => "",
            'full_name' => "",
        ]);

        $response->assertCookie('laravel_session')
                ->assertStatus(422)
                ->assertJsonStructure(['message'])
                ->assertJsonValidationErrors([
                    'email',
                    'item_description',
                    'where',
                    'when',
                    'message',
                    'full_name',
                ]);
    }

    //dito
    /**
     * Test guest user can return post even unauthenticated via API.
     * 
     * This test verifies that a guest user can return post even unauthenticated via API endpoint.
     */
    public function test_guest_user_can_return_post_even_unauthenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

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

        $category = Category::create(['name' => "Sample Category"]);
        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory"
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'type' => "Lost",
            'subcategory_id' => $subCategory->id,
            'incident_location' => 'Manila City',
            'incident_date' => '2024-01-02',
            'status' => PostStatus::ON_PROCESSING
        ]);

        $guestReturnerEmail = 'test555@gmail.com';

        Mail::fake();

        $response = $this->postJson(route('api.v1.public.return', $post->id), [
            'email' => $guestReturnerEmail,
            'item_description' => "Testing Description",
            'where' => "Quezon City",
            'when' => "December 5, 2024",
            'message' => "Test Message",
            'full_name' => "John Doe",
        ]);

        Mail::assertSent(ReturnRequested::class, function (ReturnRequested $mail) use ($user, $guestReturnerEmail) {
            return $mail->hasSubject('QuestRetrieve - Someone wants to return your lost item') &&
                $mail->hasFrom($guestReturnerEmail) && 
                $mail->hasReplyTo($user->email);
        });

        $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully Return Requested.']);
    }

    /**
     * Test user can return post while authenticated via API.
     * 
     * This test verifies that a user can return post while authenticated via API endpoint.
     */
    public function test_user_can_return_post_even_authenticated(): void
    {
        $role = Role::where('id', UserType::PUBLIC_USER)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        
        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');
        $csrf->assertCookie('XSRF-TOKEN');

        $category = Category::create(['name' => "Sample Category"]);
        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory"
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'type' => "Lost",
            'subcategory_id' => $subCategory->id,
            'incident_location' => 'Manila City',
            'incident_date' => '2024-01-02',
            'status' => PostStatus::ON_PROCESSING
        ]);

        $guestReturnerEmail = 'test555@gmail.com';

        Mail::fake();

        $response = $this->postJson(route('api.v1.public.return', $post->id), [
            'email' => $guestReturnerEmail,
            'item_description' => "Testing Description",
            'where' => "Quezon City",
            'when' => "December 5, 2024",
            'message' => "Test Message",
            'full_name' => "John Doe",
        ]);

        Mail::assertSent(ReturnRequested::class, function (ReturnRequested $mail) use ($user, $guestReturnerEmail) {
            return $mail->hasSubject('QuestRetrieve - Someone wants to return your lost item') &&
                $mail->hasFrom($guestReturnerEmail) && 
                $mail->hasReplyTo($user->email);
        });

        $response->assertStatus(200)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'Successfully Return Requested.']);
    }

    /**
     * Test not public user cannot return post via API.
     * 
     * This test verifies that not public user cannot return post via API endpoint.
     */
    public function test_not_public_user_cannot_return_post(): void
    {
        $role = Role::where('id', UserType::ADMINISTRATOR)->first();

        if (!$role) {
            $this->fail('Role Public User not found in the database.');
        }

        $this->get('/sanctum/csrf-cookie')->assertCookie('XSRF-TOKEN');

        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'role_id' => $role->id
        ]);

        
        $this->post('/api/v1/authentication/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);

        Profile::create([
            'user_id' => $user->id,
            'last_name' => "Doe",
            'first_name' => "Rick",
            'birthday' => "2024-05-19",
            'contact_no' => "12345",
        ]);

        $csrf = $this->get('/sanctum/csrf-cookie');
        $csrf->assertCookie('XSRF-TOKEN');

        $category = Category::create(['name' => "Sample Category"]);
        $subCategory = Subcategory::create([
            'category_id' => $category->id,
            'name' => "Sample Subcategory"
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'type' => "Lost",
            'subcategory_id' => $subCategory->id,
            'incident_location' => 'Manila City',
            'incident_date' => '2024-01-02',
            'status' => PostStatus::ON_PROCESSING
        ]);

        $guestClaimerEmail = 'test555@gmail.com';

        $response = $this->postJson(route('api.v1.public.claim', $post->id), [
            'email' => $guestClaimerEmail,
            'item_description' => "Testing Description",
            'where' => "Quezon City",
            'when' => "December 5, 2024",
            'message' => "Test Message",
            'full_name' => "John Doe",
        ]);


        $response->assertCookie('laravel_session')
                ->assertStatus(403)
                ->assertJsonStructure(['message'])
                ->assertJson(['message' => 'You are not allowed to access this action']);
    }
}
