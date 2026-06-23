<?php

namespace App\Enum\Game;

enum GameRuntimeStepEnum: string
{
    case SETUP = 'setup';

    case NIGHT = 'night';
    case DAY = 'day';
    case VOTE = 'vote';
    case INTERUPT = 'interupt';

    case FINISH = 'finish';
}
