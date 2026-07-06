<?php

namespace App\Domain\Command\Game\Runtime;

class CupidonSetupCommand
{
    public function __construct(
        public readonly string $firstLoverId,
        public readonly string $secondLoverId,
    ) {
    }
}
