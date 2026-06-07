<?php

namespace App\Fixtures\Factory\Game\Period\Vote;

use App\Entity\Game\Period\Vote\Ballot;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Ballot>
 */
final class BallotFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Ballot::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [];
    }
}
