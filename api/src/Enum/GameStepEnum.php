<?php

namespace App\Enum;

enum GameStepEnum: string
{
    case INITIALISATION = 'initialisation';
    case DAY = 'day';
    case NIGHT = 'night';
    case FINISHED = 'finished';
}
