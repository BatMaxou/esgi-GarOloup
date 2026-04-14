<?php

namespace App\Enum\Game;

enum GameInitialisationStepEnum: string
{
    case NEW = 'new';

    case CONFIGURATION = 'configuration';
    case GAME_MASTER_CHOICE = 'game_master_choice';
    case DISPATCH = 'dispatch';

    case FINISH = 'finish';
}
