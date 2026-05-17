<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Domain\Workflow\WorkflowBuilder;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\GameRoleInitializedStory;
use App\Fixtures\Story\RoleInitializedStory;
use App\Tests\Helper\Builder\Game\Role\GameRoleBuilderBag;
use App\Tests\Helper\Builder\Role\RoleBuilderBag;
use App\Tests\Helper\ThereIs;
use Doctrine\ORM\EntityManagerInterface;
use Zenstruck\Foundry\Story;

class ClassicGameConfiguredStory extends Story
{
    public const HOST = 'host';
    public const WEREWOLF1 = 'werewolf1';
    public const WEREWOLF2 = 'werewolf2';
    public const VILLAGER1 = 'villager1';
    public const VILLAGER2 = 'villager2';
    public const VILLAGER3 = 'villager3';
    public const VILLAGER4 = 'villager4';
    public const GAME = 'game';

    public const PLAYERS_POOL = 'players';

    public function __construct(
        protected readonly WorkflowBuilder $workflowBuilder,
        protected readonly EntityManagerInterface $em,
    ) {
    }

    public function build(): void
    {
        $roleBagBuilder = RoleInitializedStory::load()->getState(RoleInitializedStory::ROLE_BAG);
        \assert($roleBagBuilder instanceof RoleBuilderBag);

        $gameRoleBagBuilder = GameRoleInitializedStory::load()->getState(GameRoleInitializedStory::GAME_ROLE_BAG);
        \assert($gameRoleBagBuilder instanceof GameRoleBuilderBag);

        $hostBuilder = ThereIs::anUser()->withEmail('classic-game-host@garoloup.com')->build();
        $hostPlayerBuilder = ThereIs::aPlayer()
            ->withUser($hostBuilder)
            ->withRole($gameRoleBagBuilder->getVillager())
            ->build();

        $temp1Builder = ThereIs::aTempUser()->withUsername('classic-game-temp1')->build();
        $temp1PlayerBuilder = ThereIs::aPlayer()
            ->withTempUser($temp1Builder)
            ->withRole($gameRoleBagBuilder->getWerewolf())
            ->build();
        $temp2Builder = ThereIs::aTempUser()->withUsername('classic-game-temp2')->build();
        $temp2PlayerBuilder = ThereIs::aPlayer()
            ->withTempUser($temp2Builder)
            ->withRole($gameRoleBagBuilder->getVillager())
            ->build();
        $temp3Builder = ThereIs::aTempUser()->withUsername('classic-game-temp3')->build();
        $temp3PlayerBuilder = ThereIs::aPlayer()
            ->withTempUser($temp3Builder)
            ->withRole($gameRoleBagBuilder->getVillager())
            ->build();
        $temp4Builder = ThereIs::aTempUser()->withUsername('classic-game-temp4')->build();
        $temp4PlayerBuilder = ThereIs::aPlayer()
            ->withTempUser($temp4Builder)
            ->withRole($gameRoleBagBuilder->getVillager())
            ->build();
        $temp5Builder = ThereIs::aTempUser()->withUsername('classic-game-temp5')->build();
        $temp5PlayerBuilder = ThereIs::aPlayer()
            ->withTempUser($temp5Builder)
            ->withRole($gameRoleBagBuilder->getWerewolf())
            ->build();

        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withPlayers([$hostPlayerBuilder, $temp1PlayerBuilder, $temp2PlayerBuilder, $temp3PlayerBuilder, $temp4PlayerBuilder, $temp5PlayerBuilder])
            ->withInitialisationStep(GameInitialisationStepEnum::FINISH)
            ->withRuntimeStep(GameRuntimeStepEnum::SETUP)
            ->build();

        ThereIs::aConfiguration()
            ->forGame($gameBuilder)
            ->withComposition(ThereIs::aComposition()
                ->withRole($roleBagBuilder->getVillager(), 4)
                ->withRole($roleBagBuilder->getWerewolf(), 2)
                ->build())
            ->build();

        $this->workflowBuilder->buildFor($gameBuilder->getEntity());
        $this->em->flush();

        $this->addState(self::HOST, $hostPlayerBuilder);
        $this->addState(self::WEREWOLF1, $temp1PlayerBuilder, pool: self::PLAYERS_POOL);
        $this->addState(self::WEREWOLF2, $temp5PlayerBuilder, pool: self::PLAYERS_POOL);
        $this->addState(self::VILLAGER1, $hostPlayerBuilder, pool: self::PLAYERS_POOL);
        $this->addState(self::VILLAGER2, $temp2PlayerBuilder, pool: self::PLAYERS_POOL);
        $this->addState(self::VILLAGER3, $temp3PlayerBuilder, pool: self::PLAYERS_POOL);
        $this->addState(self::VILLAGER4, $temp4PlayerBuilder, pool: self::PLAYERS_POOL);
        $this->addState(self::GAME, $gameBuilder);
    }
}
