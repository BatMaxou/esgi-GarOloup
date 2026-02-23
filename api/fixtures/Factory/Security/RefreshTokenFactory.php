<?php

namespace App\Fixtures\Factory\Security;

use App\Entity\Security\RefreshToken;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<RefreshToken>
 */
final class RefreshTokenFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return RefreshToken::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'refreshToken' => self::faker()->text(128),
            'valid' => new \DateTime('+1 month'),
        ];
    }
}
