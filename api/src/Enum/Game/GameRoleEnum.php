<?php

namespace App\Enum\Game;

enum GameRoleEnum: string
{
    case WEREWOLF = 'werewolf';
    case VILLAGER = 'villager';

    // Add doc for a role not yet playable
    case PREVIEW = 'preview';
}
