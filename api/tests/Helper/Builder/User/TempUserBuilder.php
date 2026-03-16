<?php

namespace App\Tests\Helper\Builder\User;

use App\Entity\User\TempUser;
use App\Fixtures\Factory\User\TempUserFactory;
use App\Tests\Helper\Builder\AbstractBuilder;

/** @extends AbstractBuilder<TempUser> */
class TempUserBuilder extends AbstractBuilder
{
    public ?string $username = null;
    public ?string $ip = null;

    protected function doBuild(): object
    {
        return TempUserFactory::createOne([
            ...($this->ip ? ['ip' => $this->ip] : []),
            ...($this->username ? ['username' => $this->username] : []),
        ]);
    }

    public function withIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function withUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
}
