<?php

namespace App\Fixtures\Factory\User;

use App\Entity\User\User;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @template T of User
 *
 * @extends PersistentObjectFactory<T>
 */
abstract class AbstractUserFactory extends PersistentObjectFactory
{
    public const DEFAULT_TEST_PASSWORD = 'azertyuiAZ123#';

    #[\Override]
    abstract public static function class(): string;

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
