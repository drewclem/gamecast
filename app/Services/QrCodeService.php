<?php

namespace App\Services;

use App\Models\Game;
use App\Models\Show;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
  /**
   * Generate a QR code for a game's join URL
   *
   * @param Game $game
   * @return string Public URL of the QR code
   */
  public function generateGameJoinQrCode(Game $game): string
  {
    $url = route('games.join', ['game' => $game->slug]);
    $filename = "games/{$game->id}/join-qr.png";

    return $this->generateAndSaveQrCode($url, $filename);
  }

  /**
   * Generate a QR code and save it to storage
   *
   * @param string $url
   * @param string $filename
   * @param array $options
   * @return string Public URL of the QR code
   */
  public function generateAndSaveQrCode(string $url, string $filename, array $options = []): string
  {
    $defaultOptions = [
      'format' => 'png',
      'size' => 500,
      'margin' => 1,
      'errorCorrection' => 'H',
      'backgroundColor' => [255, 255, 255],
      'foregroundColor' => [0, 0, 0],
    ];

    $options = array_merge($defaultOptions, $options);

    // Generate QR code
    $qrCode = QrCode::format($options['format'])
      ->size($options['size'])
      ->margin($options['margin'])
      ->errorCorrection($options['errorCorrection'])
      ->backgroundColor($options['backgroundColor'][0], $options['backgroundColor'][1], $options['backgroundColor'][2])
      ->color($options['foregroundColor'][0], $options['foregroundColor'][1], $options['foregroundColor'][2])
      ->generate($url);

    // Save to storage
    Storage::disk('public')->put($filename, $qrCode);

    // Return public URL
    return asset('storage/' . $filename);
  }

  /**
   * Delete a QR code from storage
   *
   * @param string $filename
   * @return bool
   */
  public function deleteQrCode(string $filename): bool
  {
    return Storage::disk('public')->delete($filename);
  }
}
