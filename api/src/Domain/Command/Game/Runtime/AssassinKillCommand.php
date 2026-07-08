<?php

namespace App\Domain\Command\Game\Runtime;

class AssassinKillCommand
{
    public function __construct(
        public readonly string $targetPlayerId,
    ) {
    }
}
