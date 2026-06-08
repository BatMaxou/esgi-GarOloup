<?php

namespace App\Domain\Command\Game\Runtime;

class WitchPoisonCommand
{
    public function __construct(
        public readonly string $targetPlayerId,
    ) {
    }
}
