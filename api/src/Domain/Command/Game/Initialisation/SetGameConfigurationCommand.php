<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Api\Model\Game\Composition\CompositionInput;

class SetGameConfigurationCommand
{
    public function __construct(
        public readonly ?CompositionInput $composition = null,
        public readonly bool $withGameMaster = false,
        public readonly bool $withRandomDispatch = true,
    ) {
    }
}
