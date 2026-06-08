<?php

namespace App\Domain\Command\Game\Runtime;

class WitchSaveCommand
{
    public function __construct(
        public readonly string $targetPlayerId,
    ) {
    }
}
