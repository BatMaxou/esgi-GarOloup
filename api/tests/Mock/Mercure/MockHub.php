<?php

namespace App\Tests\Mock\Mercure;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Update;

class MockHub implements HubInterface
{
    public function getPublicUrl(): string
    {
        return 'http://mercure/dummy';
    }

    public function getFactory(): ?TokenFactoryInterface
    {
        return null;
    }

    public function publish(Update $update): string
    {
        return 'id';
    }
}
