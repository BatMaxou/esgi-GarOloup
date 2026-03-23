<?php

namespace App\Enum\Game;

enum GameTeamEnum: string
{
    case VILLAGE = 'village';
    case WEREWOLF = 'werewolf';
    case SOLO = 'solo';
    case COUPLE = 'couple';
}
