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
}
