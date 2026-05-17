<?php

namespace App\Domain\Command\Game\Runtime;

class WerewolfVoteCommand
{
    public function __construct(
        public readonly string $targetPlayerId,
    ) {
    }
}
