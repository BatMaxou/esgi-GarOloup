<?php

namespace App\Enum\Game;

enum GameStepEnum: string
{
    case NEW = 'new';

    case CONFIGURATION = 'configuration';
    case GAME_MASTER_CHOICE = 'game_master_choice';

    case DISPATCH = 'dispatch';
    case LAUNCH = 'launch';

    case NIGHT = 'night';
    case DAY = 'day';

    case FINISH = 'finish';
}
