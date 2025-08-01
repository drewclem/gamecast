<?php

namespace Database\Factories;

use App\Models\Show;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Host>
 */
class HostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fn() => User::factory(),
            'show_id' => fn() => Show::factory(),
            'name' => fake()->name(),
            'avatar' => fake()->imageUrl(),
            'color' => fake()->colorName(),
        ];
    }
}
