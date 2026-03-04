<?php

namespace App\Domain\Command\User;

class ForgotPasswordCommand
{
    public function __construct(
        public string $email,
    ) {
    }
}
