<?php

namespace App\Tests\Helper\Builder\User;

use App\Fixture\Factory\UserFactory;
use App\Tests\Helper\Builder\AbstractBuilder;

/** @extends AbstractBuilder<User> */
class UserBuilder extends AbstractBuilder
{
    public ?string $username = null;
    public ?string $password = null;
    public ?string $email = null;

    protected function doBuild(): object
    {
        return UserFactory::createOne([
            ...($this->username ? ['username' => $this->username] : []),
            ...($this->password ? ['password' => $this->password] : []),
            ...($this->email ? ['email' => $this->email] : []),
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
}
