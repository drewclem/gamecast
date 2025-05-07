<?php

namespace App\Enums;

enum GameStatus: string
{
    case UPCOMING = 'upcoming';
    case LIVE = 'live';
    case ENDED = 'ended';

    public static function getStatuses(): array
    {
        return array_map(fn(GameStatus $status) => $status->value, GameStatus::cases());
    }

    public function label(): string
    {
        return match ($this) {
            self::UPCOMING => 'Upcoming',
            self::LIVE => 'Live',
            self::ENDED => 'Ended',
        };
    }
}
