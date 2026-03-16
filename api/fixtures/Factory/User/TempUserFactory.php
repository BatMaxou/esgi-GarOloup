<?php

namespace App\Fixtures\Factory\User;

use App\Entity\User\TempUser;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<TempUser>
 */
final class TempUserFactory extends PersistentObjectFactory
{
    public const DEFAULT_TEST_IP = '127.0.0.1';

    #[\Override]
    public static function class(): string
    {
        return TempUser::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'username' => self::faker()->userName(),
            'ip' => self::DEFAULT_TEST_IP,
        ];
    }
}
