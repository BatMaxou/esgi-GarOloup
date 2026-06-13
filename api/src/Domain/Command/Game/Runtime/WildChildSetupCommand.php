<?php

namespace App\Domain\Command\Game\Runtime;

class WildChildSetupCommand
{
    public function __construct(
        public readonly string $targetPlayerId,
    ) {
    }
}
