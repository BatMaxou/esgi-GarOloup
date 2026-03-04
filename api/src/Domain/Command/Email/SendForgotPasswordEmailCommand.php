<?php

namespace App\Domain\Command\Email;

use App\Entity\User\User;

class SendForgotPasswordEmailCommand
{
    public function __construct(
        public User $user,
        public string $token,
    ) {
    }
}
