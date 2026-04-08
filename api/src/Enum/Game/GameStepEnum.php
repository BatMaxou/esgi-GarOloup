<?php

namespace App\Enum\Game;

enum GameStepEnum: string
{
    case NEW = 'new';

    case CONFIGURATION = 'configuration';
    case GAME_MASTER_CHOICE = 'game_master_choice';
    case DISPATCH = 'dispatch';
    case READY = 'ready';

    case SETUP = 'setup';

    case NIGHT = 'night';
    case DAY = 'day';
    case VOTE = 'vote';

    case FINISH = 'finish';
}
