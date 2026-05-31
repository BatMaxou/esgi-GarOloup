<?php

namespace App\Domain\Command\Game\Runtime;

class VoteCommand
{
    public function __construct(
        public readonly string $targetPlayerId,
    ) {
    }
}
