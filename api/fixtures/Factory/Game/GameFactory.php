<?php

namespace App\Fixtures\Factory\Game;

use App\Entity\Game\Game;
use App\Enum\Game\GameInitialisationStepEnum;
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
            'initialisationStep' => GameInitialisationStepEnum::NEW,
            'maxPlayers' => 10,
            'maxTimeForDiscussion' => 5,
        ];
    }

    #[\Override]
    protected function initialize(): static
    {
        return $this->afterInstantiate(function (Game $game): void {
            $host = $game->getHost();
            if (!$game->getPlayers()->contains($host) && $game->getGameMaster() !== $host) {
                $game->addPlayer($host);
            }
        });
    }
}
