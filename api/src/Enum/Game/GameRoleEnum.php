<?php

namespace App\Enum\Game;

enum GameRoleEnum: string
{
    case WEREWOLF = 'werewolf';
    case VILLAGER = 'villager';
    case SEER = 'seer';
    case WITCH = 'witch';
    case HUNTER = 'hunter';
    case LOOKALIKE = 'lookalike';
    case THIEF = 'thief';

    public function getPriority(): ?int
    {
        return match ($this) {
            self::VILLAGER,
            self::HUNTER => null,
            self::THIEF => 1,
            self::LOOKALIKE => 2,
            self::SEER => 3,
            self::WEREWOLF => 4,
            self::WITCH => 5,
        };
    }
}
