<?php

namespace App\Tests\Helper;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Tests\Helper\Behavior\Security\AuthBehavior;
use App\Tests\Helper\Builder\User\UserBuilder;

class When
{
    private static Client $client;

    public static function setClient(Client $client): void
    {
        static::$client = $client;
    }

    public static function auth(): AuthBehavior
    {
        return new AuthBehavior(static::$client);
    }

    public static function asUser(UserBuilder $userBuilder): static
    {
        $response = static::auth()->login($userBuilder->email, $userBuilder->password);

        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Login failed.');
        }

        $data = json_decode($response->getContent(false), true);

        $token = $data['token'] ?? null;
        if (null === $token) {
            throw new \RuntimeException('Auth token not found.');
        }

        static::$client = static::$client->withOptions([
            'headers' => [
                'Authorization' => \sprintf('Bearer %s', $token),
            ],
        ]);

        static::$client->disableReboot();

        return new static();
    }

    public function asAnonymous(): static
    {
        static::$client = static::$client->withOptions([
            'headers' => [
                'Authorization' => '',
            ],
        ]);

        static::$client->disableReboot();

        return new static();
    }
}
