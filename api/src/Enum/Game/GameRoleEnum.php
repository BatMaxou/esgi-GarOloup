<?php

namespace App\Enum\Game;

enum GameRoleEnum: string
{
    case WEREWOLF = 'werewolf';
    case VILLAGER = 'villager';
    case SEER = 'seer';
    case WITCH = 'witch';
    case HUNTER = 'hunter';
    case WILD_CHILD = 'wild_child';
    case INFECT_FATHER = 'infect_father';
    case CUPIDON = 'cupidon';
    case ASSASSIN = 'assassin';

    // TODO: To be implemented
    case LOOKALIKE = 'lookalike';
    case THIEF = 'thief';
    case DICTATOR = 'dictator';

    public function getNightPriority(): ?int
    {
        return match ($this) {
            self::VILLAGER,
            self::HUNTER,
            self::DICTATOR,
            self::CUPIDON,
            self::WILD_CHILD => null,
            self::THIEF => 1,
            self::LOOKALIKE => 2,
            self::SEER => 3,
            self::WEREWOLF => 4,
            self::INFECT_FATHER => 5,
            self::WITCH => 6,
            self::ASSASSIN => 7,
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
            self::WILD_CHILD,
            self::CUPIDON,
            self::INFECT_FATHER,
            self::ASSASSIN => null,
            self::DICTATOR => 1,
        };
    }

    public function getTeam(): GameTeamEnum
    {
        return match ($this) {
            self::WEREWOLF,
            self::INFECT_FATHER => GameTeamEnum::WEREWOLF,
            self::VILLAGER,
            self::SEER,
            self::WITCH,
            self::HUNTER,
            self::LOOKALIKE,
            self::THIEF,
            self::DICTATOR,
            self::CUPIDON,
            self::WILD_CHILD => GameTeamEnum::VILLAGE,
            self::ASSASSIN => GameTeamEnum::SOLO,
        };
    }
}
