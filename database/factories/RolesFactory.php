<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Roles;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\\Models\\Roles>
 */
class RolesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Roles::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['admin', 'manager', 'user']),
            'description' => $this->faker->sentence(),
        ];
    }
}
