<?php

namespace App\Tests\Helper;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Fixtures\Factory\UserFactory;
use App\Tests\Helper\Behavior\Game\GameBehavior;
use App\Tests\Helper\Behavior\Security\AuthBehavior;
use App\Tests\Helper\Behavior\User\TempUserBehavior;
use App\Tests\Helper\Builder\User\TempUserBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;

final class When
{
    private static Client $client;

    public static function setClient(Client $client): void
    {
        self::$client = $client;
    }

    public static function auth(): AuthBehavior
    {
        return new AuthBehavior(self::$client);
    }

    public static function tempUser(): TempUserBehavior
    {
        return new TempUserBehavior(self::$client);
    }

    public static function game(): GameBehavior
    {
        return new GameBehavior(self::$client);
    }

    public static function asUser(UserBuilder $userBuilder): static
    {
        $email = $userBuilder->email ?? $userBuilder->getEntity()->getEmail();
        $password = $userBuilder->password ?? UserFactory::DEFAULT_TEST_PASSWORD;

        $response = self::auth()->login($email, $password);
        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Login failed.');
        }

        return self::as($response->get('[token]'));
    }

    public static function asTempUser(TempUserBuilder $tempUserBuilder): static
    {
        $response = self::tempUser()->get($tempUserBuilder->username ?? $tempUserBuilder->getEntity()->getUsername());
        if (200 !== $response->getStatusCode()) {
            throw new \RuntimeException('Temp user retrieval failed.');
        }

        return self::as($response->get('[token]'));
    }

    public static function asAnonymous(): static
    {
        return self::as(null);
    }

    private static function as(?string $token): static
    {
        self::$client = static::$client->withOptions([
            'headers' => [
                ...(empty($token) ? [] : ['Authorization' => \sprintf('Bearer %s', $token)]),
            ],
        ]);

        self::$client->disableReboot();

        return new static();
    }
}
