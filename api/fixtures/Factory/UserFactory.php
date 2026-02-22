<?php

namespace App\Fixture\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<User> */
final class UserFactory extends PersistentObjectFactory
{
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
            'password' => 'azertyuiAZ123#',
            'username' => self::faker()->userName(),
        ];
    }
}
