<?php

namespace App\Fixtures\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<User> */
final class UserFactory extends PersistentObjectFactory
{
    public const DEFAULT_TEST_PASSWORD = 'azertyuiAZ123#';

    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return User::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->email(),
            'password' => self::DEFAULT_TEST_PASSWORD,
            'username' => self::faker()->userName(),
        ];
    }
}
