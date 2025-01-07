<?php

namespace Tests\Feature\Public\Post;

use App\Enums\PostStatus;
use App\Enums\PostType;
use App\Enums\UserType;
use App\Mail\PostCreated;
use App\Mail\PostDeactivated;
use App\Models\Category;
use App\Models\Post;
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
}
