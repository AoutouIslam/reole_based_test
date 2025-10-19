<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roles;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
           $roles = [
            ['name' => 'admin', 'description' => 'Full access to everything'],
            ['name' => 'manager', 'description' => 'Can manage teams and reports'],
            ['name' => 'user', 'description' => 'Regular user access'],
        ];

        foreach ($roles as $role) {
            Roles::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
