<?php

use App\Models\Game;
use App\Models\Host;
use App\Models\Show;
use App\Models\User;
use App\Models\Question;
use App\Enums\QuestionStatus;
use Database\Factories\GameFactory;
use Database\Factories\HostFactory;
use Database\Factories\ShowFactory;
use Database\Factories\UserFactory;
use Database\Factories\QuestionFactory;
use function Pest\Laravel\{actingAs, put};
use App\Http\Controllers\UpdateGameQuestionController;


beforeEach(function () {
  $this->user = UserFactory::new()->create();
  $this->show = ShowFactory::new()->for($this->user)->create();
  $this->host = HostFactory::new()->for($this->user)->for($this->show)->create();

  User::where('id', $this->user->id)->update(['current_host_id' => $this->host->id]);
  $this->user = User::with('currentHost.show')->find($this->user->id);

  $this->game = GameFactory::new()->for($this->show)->create();
  $this->question = QuestionFactory::new()->for($this->game)->for($this->host)->create([
    'status' => QuestionStatus::PENDING,
  ]);
});

test('it updates question status', function () {
  actingAs($this->user);

  $response = put(
    route('games.questions.update', [
      'game' => $this->game->slug,
      'question' => $this->question->id,
    ]),
    [
      'status' => QuestionStatus::ACTIVE->value,
    ]
  );
  $this->question->refresh();

  $response->assertRedirect();
  expect(Question::find($this->question->id))
    ->status->toBe(QuestionStatus::ACTIVE->value);
})->covers(UpdateGameQuestionController::class);

test('it validates status is valid', function () {
  actingAs($this->user);

  $response = put(
    route('games.questions.update', [
      'game' => $this->game->slug,
      'question' => $this->question->id,
    ]),
    [
      'status' => 'invalid-status',
    ]
  );

  $response->assertSessionHasErrors('status');
  expect(Question::find($this->question->id))
    ->status->toBe(QuestionStatus::PENDING->value);
})->covers(UpdateGameQuestionController::class);

test('it prevents unauthorized access', function () {
  $otherUser = UserFactory::new()->create();
  $otherShow = ShowFactory::new()->for($otherUser)->create();
  $otherHost = HostFactory::new()->for($otherUser)->for($otherShow)->create();
  User::where('id', $otherUser->id)->update(['current_host_id' => $otherHost->id]);
  $otherUser = User::with('currentHost.show')->find($otherUser->id);

  actingAs($otherUser);

  $response = put(
    route('games.questions.update', [
      'game' => $this->game->slug,
      'question' => $this->question->id,
    ]),
    [
      'status' => QuestionStatus::ACTIVE->value,
    ]
  );

  $response->assertForbidden();
  expect(Question::find($this->question->id))
    ->status->toBe(QuestionStatus::PENDING->value);
})->covers(UpdateGameQuestionController::class);
