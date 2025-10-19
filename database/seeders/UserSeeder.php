<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Hash; 


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $roles = Roles::all();

        if ($roles->count() === 0) {
            $this->call(RoleSeeder::class);
            $roles = Roles::all();
        }

        // Create one admin manually
        $adminRole = Roles::where('name', 'admin')->first();
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // Create 20 random users with random roles
        User::factory()->count(500)->create()->each(function ($user) use ($roles) {
            $user->role_id = $roles->random()->id;
            $user->save();
        });
    
    }
}
