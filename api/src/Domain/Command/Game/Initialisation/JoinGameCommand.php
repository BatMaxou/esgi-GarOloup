<?php

namespace App\Domain\Command\Game\Initialisation;

class JoinGameCommand
{
    public function __construct(
        public readonly string $joinCode,
    ) {
    }
}
