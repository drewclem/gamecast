<?php

use App\Models\Game;
use App\Models\Show;
use App\Services\QrCodeService;
use Database\Factories\GameFactory;
use Database\Factories\ShowFactory;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
  $this->service = new QrCodeService();
  Storage::fake('public');
});

test('it generates and saves game join qr code', function () {
  $game = GameFactory::new()->create();

  $url = $this->service->generateGameJoinQrCode($game);

  expect($url)
    ->toBeString()
    ->toContain("/storage/games/{$game->slug}/join-qr.png");

  Storage::disk('public')->assertExists("games/{$game->slug}/join-qr.png");
});

test('it generates and saves show join qr code', function () {
  $show = ShowFactory::new()->create();

  $url = $this->service->generateShowJoinQrCode($show);

  expect($url)
    ->toBeString()
    ->toContain("/storage/shows/{$show->slug}/join-qr.png");

  Storage::disk('public')->assertExists("shows/{$show->slug}/join-qr.png");
});

test('it can delete qr code', function () {
  $game = GameFactory::new()->create();
  $filename = "games/{$game->slug}/join-qr.png";

  // Generate QR code
  $this->service->generateGameJoinQrCode($game);
  Storage::disk('public')->assertExists($filename);

  // Delete QR code
  $this->service->deleteQrCode($filename);
  Storage::disk('public')->assertMissing($filename);
});
