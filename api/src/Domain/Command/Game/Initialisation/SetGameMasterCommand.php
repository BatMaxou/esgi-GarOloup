<?php

namespace App\Domain\Command\Game\Initialisation;

class SetGameMasterCommand
{
    public function __construct(
        public readonly string $playerId,
    ) {
    }
}
