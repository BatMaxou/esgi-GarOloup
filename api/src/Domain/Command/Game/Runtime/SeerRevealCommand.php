<?php

namespace App\Domain\Command\Game\Runtime;

class SeerRevealCommand
{
    public function __construct(
        public readonly string $targetPlayerId,
    ) {
    }
}
