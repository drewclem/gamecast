<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Host;
use App\Enums\QuestionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'question' => fake()->sentence(),
      'status' => QuestionStatus::PENDING,
      'is_active' => true,
      'order_index' => 0,
      'game_id' => fn() => Game::factory(),
      'host_id' => fn() => Host::factory(),
    ];
  }
}
