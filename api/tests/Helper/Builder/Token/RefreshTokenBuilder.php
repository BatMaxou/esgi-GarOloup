<?php

namespace App\Tests\Helper\Builder\Token;

use App\Entity\Security\RefreshToken;
use App\Fixtures\Factory\Security\RefreshTokenFactory;
use App\Tests\Helper\Builder\AbstractBuilder;

/** @extends AbstractBuilder<RefreshToken> */
class RefreshTokenBuilder extends AbstractBuilder
{
    public ?string $username = null;
    public ?string $token = null;
    public ?\DateTimeInterface $valid = null;

    protected function doBuild(): object
    {
        return RefreshTokenFactory::createOne([
            ...($this->username ? ['username' => $this->username] : []),
            ...($this->token ? ['refreshToken' => $this->token] : []),
            ...($this->valid ? ['valid' => $this->valid] : []),
        ]);
    }

    public function withUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function withToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function validFor(string $valid): static
    {
        $this->valid = new \DateTimeImmutable($valid);

        return $this;
    }

    public function expired(): static
    {
        $this->valid = new \DateTimeImmutable('-1 day');

        return $this;
    }
}
