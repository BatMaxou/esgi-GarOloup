<?php

namespace App\Domain\Command\User;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordCommand
{
    #[Assert\Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/')]
    public string $password;

    public function __construct(
        public string $token,
        string $password,
    ) {
        $this->password = $password;
    }
}
