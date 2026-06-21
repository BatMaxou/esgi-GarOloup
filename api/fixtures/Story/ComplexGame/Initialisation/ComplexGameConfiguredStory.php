<?php

namespace App\Fixtures\Story\ComplexGame\Initialisation;

use App\Enum\Game\GameInitialisationStepEnum;
use App\Fixtures\Story\Role\RoleInitializedStory;
use App\Tests\Helper\Builder\Game\CompositionBuilder;
use App\Tests\Helper\Builder\Game\ConfigurationBuilder;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Role\RoleBuilderBag;
use App\Tests\Helper\ThereIs;

class ComplexGameConfiguredStory extends ComplexGameClosedStory
{
    public const ROLE_BAG = 'role_bag';
    public const COMPOSITION = 'composition';
    public const CONFIGURATION = 'configuration';

    public function build(): void
    {
        parent::build();

        $roleBagBuilder = ThereIs::aStory(RoleInitializedStory::class)
            ->execute()
            ->getState(RoleInitializedStory::ROLE_BAG);
        \assert($roleBagBuilder instanceof RoleBuilderBag);
        $this->addState(self::ROLE_BAG, $roleBagBuilder);

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 6)
            ->withRole($roleBagBuilder->getSeer(), 1)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
            ->withRole($roleBagBuilder->getInfectFather(), 1)
            ->withRole($roleBagBuilder->getWitch(), 1)
            ->withRole($roleBagBuilder->getWildChild(), 1);
        $this->addState(self::COMPOSITION, $compositionBuilder);

        $configurationBuilder = ThereIs::aConfiguration()
            ->forGame($gameBuilder)
            ->withComposition($compositionBuilder);
        $this->addState(self::CONFIGURATION, $configurationBuilder);

        $gameBuilder->withInitialisationStep(GameInitialisationStepEnum::DISPATCH);
    }

    public function execute(): void
    {
        parent::execute();

        $compositionBuilder = $this->getState(self::COMPOSITION);
        \assert($compositionBuilder instanceof CompositionBuilder);
        $compositionBuilder->build();

        $configurationBuilder = $this->getState(self::CONFIGURATION);
        \assert($configurationBuilder instanceof ConfigurationBuilder);
        $configurationBuilder->build();
    }

    public function getPrefix(): string
    {
        return 'complex-game-configured-';
    }
}
