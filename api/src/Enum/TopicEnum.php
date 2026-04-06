<?php

namespace App\Enum;

enum TopicEnum: string
{
    case CURRENT_GAME = 'garoloup-game-{id}';
    case CURRENT_PLAYER = 'garoloup-player-{id}';
    case UNKNOWN = 'garoloup-unknown';
}
