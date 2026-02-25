<?php

namespace App\Fixtures\Factory;

use App\Entity\Player;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Player>
 */
final class PlayerFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Player::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'user' => null,
            'tempUser' => TempUserFactory::new(),
            'dead' => false,
        ];
    }
}
