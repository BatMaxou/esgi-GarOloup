<?php

namespace App\Tests\Helper\Builder\User;

use App\Entity\User\User;
use App\Fixtures\Factory\User\UserFactory;
use App\Tests\Helper\Builder\AbstractBuilder;

/** @extends AbstractBuilder<User> */
class UserBuilder extends AbstractBuilder
{
    public ?string $username = null;
    public ?string $password = null;
    public ?string $email = null;
    public ?string $resetToken = null;

    protected function doBuild(): object
    {
        return UserFactory::createOne([
            ...($this->username ? ['username' => $this->username] : []),
            // use setPassword(...)
            ...($this->password ? ['password' => $this->password] : []),
            ...($this->email ? ['email' => $this->email] : []),
            ...($this->resetToken ? ['resetToken' => $this->resetToken] : []),
        ]);
    }

    public function withUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function withPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function withEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function withResetToken(string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }
}
