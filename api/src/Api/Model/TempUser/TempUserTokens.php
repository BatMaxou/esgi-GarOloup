<?php

namespace App\Api\Model\TempUser;

class TempUserTokens
{
    public function __construct(
        public ?string $token = null,
        public ?string $refreshToken = null,
    ) {
    }
}
