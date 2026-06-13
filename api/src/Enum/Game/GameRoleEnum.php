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
    case DICTATOR = 'dictator';
    case WILD_CHILD = 'wild_child';

    public function getNightPriority(): ?int
    {
        return match ($this) {
            self::VILLAGER,
            self::HUNTER,
            self::DICTATOR,
            self::WILD_CHILD => null,
            self::THIEF => 1,
            self::LOOKALIKE => 2,
            self::SEER => 3,
            self::WEREWOLF => 4,
            self::WITCH => 5,
        };
    }

    public function getDayPriority(): ?int
    {
        return match ($this) {
            self::VILLAGER,
            self::HUNTER,
            self::THIEF,
            self::LOOKALIKE,
            self::SEER,
            self::WEREWOLF,
            self::WITCH,
            self::WILD_CHILD => null,
            self::DICTATOR => 1,
        };
    }

    public function getTeam(): GameTeamEnum
    {
        return match ($this) {
            self::WEREWOLF => GameTeamEnum::WEREWOLF,
            self::VILLAGER,
            self::SEER,
            self::WITCH,
            self::HUNTER,
            self::LOOKALIKE,
            self::THIEF,
            self::DICTATOR,
            self::WILD_CHILD => GameTeamEnum::VILLAGE,
        };
    }
}
