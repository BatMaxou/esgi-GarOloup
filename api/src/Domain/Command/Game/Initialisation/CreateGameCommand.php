<?php

namespace App\Domain\Command\Game\Initialisation;

class CreateGameCommand
{
    public function __construct(
        public readonly int $maxPlayers,
        public readonly int $maxTimeForDiscussion,
        public readonly bool $public = false,
    ) {
    }
}
