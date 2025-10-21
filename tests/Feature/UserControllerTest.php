<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash; 
use Laravel\Sanctum\Sanctum;
use App\Http\Controllers\Api\Rule;

class UserControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
     use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required roles
        Roles::factory()->create(['name' => 'admin']);
        Roles::factory()->create(['name' => 'manager']);
        Roles::factory()->create(['name' => 'user']);
    }

    /** @test */
    public function non_admin_cannot_create_user()
    {
        $user = User::factory()->create([
            'role_id' => Roles::where('name', 'user')->first()->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'role_id' => Roles::where('name', 'user')->first()->id,
        ]);

        $response->assertStatus(403);
    }

   
    /** @test */
    public function admin_can_delete_user()
    {
        $admin = User::factory()->create([
            'role_id' => Roles::where('name', 'admin')->first()->id,
        ]);

        Sanctum::actingAs($admin);

        $user = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'User deleted']);

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /** @test */
    public function non_admin_cannot_delete_user()
    {
        $normalUser = User::factory()->create([
            'role_id' => Roles::where('name', 'user')->first()->id,
        ]);

        Sanctum::actingAs($normalUser);

        $target = User::factory()->create();

        $response = $this->deleteJson("/api/users/{$target->id}");

        $response->assertStatus(403);
    }


    /** @test */
    public function admin_can_create_user()
    {
        $admin = User::factory()->create([
            'role_id' => Roles::where('name', 'admin')->first()->id,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'role_id' => Roles::where('name', 'user')->first()->id,
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'User created successfully',
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);
    }



    /** @test */
    public function pagination_returns_10_users_per_page()
    {
        $admin = User::factory()->create([
        'role_id' => Roles::where('name', 'admin')->first()->id,
    ]);

    Sanctum::actingAs($admin);

    // Create 25 users
    User::factory(25)->create();

    $response = $this->getJson('/api/users?per_page=10');

    $response->assertJsonStructure([
    'success',
    'message',
    'data' => [
        'current_page',
        'data',
        'links',
        'meta',
    ],
]);


    // Assert only 10 users are returned per page
    $this->assertCount(10, $response->json('data.data'));
    }
}
