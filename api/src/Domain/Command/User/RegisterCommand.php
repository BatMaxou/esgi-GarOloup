<?php

namespace App\Domain\Command\User;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterCommand
{
    #[Assert\Email]
    public string $email;

    #[Assert\Regex('/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/')]
    public string $password;

    public function __construct(
        public string $username,
        string $email,
        string $password,
    ) {
        $this->email = $email;
        $this->password = $password;
    }
}
