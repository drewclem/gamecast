<?php

namespace Database\Factories;

use App\Models\Show;
use App\Enums\GameStatus;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $title = fake()->sentence();
    return [
      'title' => $title,
      'slug' => Str::slug($title),
      'description' => fake()->paragraph(),
      'status' => GameStatus::UPCOMING,
      'access_password' => fake()->password(),
      'show_id' => fn() => Show::factory(),
    ];
  }
}
