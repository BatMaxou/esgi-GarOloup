<?php

namespace App\Fixtures\Factory\Game;

use App\Entity\Game\Game;
use App\Enum\Game\GameStepEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Game>
 */
final class GameFactory extends PersistentObjectFactory
{
    #[\Override]
    public static function class(): string
    {
        return Game::class;
    }

    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'host' => PlayerFactory::new(),
            'joinCode' => self::faker()->text(8),
            'step' => GameStepEnum::NEW,
            'configuration' => ConfigurationFactory::new(),
        ];
    }
}
