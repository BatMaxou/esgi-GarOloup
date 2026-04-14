<?php

namespace App\Tests\Mock\Mercure;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Update;

class MockHub implements HubInterface
{
    public function __construct(
        private TokenFactoryInterface $tokenFactory,
    ) {
    }

    public function getPublicUrl(): string
    {
        return 'http://mercure/dummy';
    }

    public function getFactory(): ?TokenFactoryInterface
    {
        return $this->tokenFactory;
    }

    public function publish(Update $update): string
    {
        return 'id';
    }
}
