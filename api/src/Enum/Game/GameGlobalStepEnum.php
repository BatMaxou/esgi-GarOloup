<?php

namespace App\Enum\Game;

enum GameGlobalStepEnum: string
{
    case NEW = 'new';
    case RUNNING = 'running';
    case FINISH = 'finish';
}
