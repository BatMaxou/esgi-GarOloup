<?php

namespace App\Domain\Command\Game\Runtime;

class HunterShootCommand
{
    public function __construct(
        public readonly string $targetPlayerId,
    ) {
    }
}
