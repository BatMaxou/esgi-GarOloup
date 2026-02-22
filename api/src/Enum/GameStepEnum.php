<?php

namespace App\Enum;

enum GameStepEnum: string
{
    case NEW = 'new';
    case GAME_MASTER_CHOICE = 'game_master_choice';
    case CONFIGURATION = 'configuration';

    case LAUNCH = 'launch';

    case NIGHT = 'night';
    case DAY = 'day';

    case FINISH = 'finish';
}
