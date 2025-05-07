<?php

namespace App\Enums;

enum QuestionStatus: string
{
    case ACTIVE = 'active';
    case CLOSED = 'closed';
    case REVEALED = 'revealed';

    public static function getStatuses(): array
    {
        return array_map(fn(QuestionStatus $status) => $status->value, QuestionStatus::cases());
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::CLOSED => 'Closed',
            self::REVEALED => 'Revealed',
        };
    }
}
