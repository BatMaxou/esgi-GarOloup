<?php

namespace App\Api\Model\Game;

class CreateGameOutput
{
    public function __construct(
        public readonly string $joinCode,
    ) {
    }
}
