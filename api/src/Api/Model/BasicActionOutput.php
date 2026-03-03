<?php

namespace App\Api\Model;

class BasicActionOutput
{
    public function __construct(
        public readonly bool $success,
    ) {
    }
}
