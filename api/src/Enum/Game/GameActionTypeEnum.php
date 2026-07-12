<?php

namespace App\Enum\Game;

enum GameActionTypeEnum: string
{
    case MURDER = 'murder';
    case SAVE = 'save';
    case INFECTION = 'infection';
    case REVEAL = 'reveal';
    case WILD_CHILD_MODEL = 'wild_child_model';
    case COUPLE = 'couple';
    case COUPLE_DEATH = 'couple_death';
    case HUNTER_SHOT = 'hunter_shot';
}
