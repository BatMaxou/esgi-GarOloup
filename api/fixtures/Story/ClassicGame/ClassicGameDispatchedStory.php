<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Enum\Game\GameInitialisationStepEnum;
use App\Fixtures\Story\Role\GameRoleInitializedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\Game\Role\GameRoleBuilderBag;
use App\Tests\Helper\Builder\Game\Role\SeerRoleBuilder;
use App\Tests\Helper\Builder\Game\Role\VillagerRoleBuilder;
use App\Tests\Helper\Builder\Game\Role\WerewolfRoleBuilder;
use App\Tests\Helper\ThereIs;

class ClassicGameDispatchedStory extends ClassicGameConfiguredStory
{
    public const VILLAGER_1 = 'villager_1';
    public const VILLAGER_2 = 'villager_2';
    public const VILLAGER_3 = 'villager_3';

    public const SEER = 'seer';

    public const WEREWOLF_1 = 'werewolf_1';
    public const WEREWOLF_2 = 'werewolf_2';

    public const VILLAGERS_POOL = 'villagers';
    public const WEREWOLVES_POOL = 'werewolves';

    public function build(): void
    {
        parent::build();

        $gameRoleBagBuilder = ThereIs::aStory(GameRoleInitializedStory::class)
            ->execute()
            ->getState(GameRoleInitializedStory::GAME_ROLE_BAG);
        \assert($gameRoleBagBuilder instanceof GameRoleBuilderBag);

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $assignments = [
            [self::VILLAGER_1, $gameRoleBagBuilder->getVillager()],
            [self::WEREWOLF_1, $gameRoleBagBuilder->getWerewolf()],
            [self::VILLAGER_2, $gameRoleBagBuilder->getVillager()],
            [self::VILLAGER_3, $gameRoleBagBuilder->getVillager()],
            [self::SEER, $gameRoleBagBuilder->getSeer()],
            [self::WEREWOLF_2, $gameRoleBagBuilder->getWerewolf()],
        ];

        foreach ($this->getPool(self::PLAYERS_POOL) as $index => $playerBuilder) {
            \assert($playerBuilder instanceof PlayerBuilder);

            [$stateName, $gameRoleBuilder] = $assignments[$index];
            $playerBuilder->withRole($gameRoleBuilder);

            $pool = match (true) {
                $gameRoleBuilder instanceof VillagerRoleBuilder => self::VILLAGERS_POOL,
                $gameRoleBuilder instanceof WerewolfRoleBuilder => self::WEREWOLVES_POOL,
                $gameRoleBuilder instanceof SeerRoleBuilder => null,
            };

            $this->addState($stateName, $playerBuilder, $pool);
        }

        $gameBuilder->withInitialisationStep(GameInitialisationStepEnum::FINISH);
    }

    public function getPrefix(): string
    {
        return 'classic-game-dispatched-';
    }
}
