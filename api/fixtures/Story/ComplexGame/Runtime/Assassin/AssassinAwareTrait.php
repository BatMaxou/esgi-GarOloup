<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Fixtures\Story\ComplexGame\Initialisation\ComplexGameConfiguredStory;
use App\Fixtures\Story\Role\GameRoleInitializedStory;
use App\Tests\Helper\Builder\Game\CompositionBuilder;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\Role\GameRoleBuilderBag;
use App\Tests\Helper\Builder\Role\RoleBuilderBag;
use App\Tests\Helper\ThereIs;

trait AssassinAwareTrait
{
    public const ASSASSIN_USER = 'assassin_user';
    public const ASSASSIN = 'assassin';

    protected function addAssassinToComposition(): void
    {
        $roleBagBuilder = $this->getState(ComplexGameConfiguredStory::ROLE_BAG);
        \assert($roleBagBuilder instanceof RoleBuilderBag);

        $compositionBuilder = $this->getState(ComplexGameConfiguredStory::COMPOSITION);
        \assert($compositionBuilder instanceof CompositionBuilder);

        $compositionBuilder->withRole($roleBagBuilder->getAssassin(), 1);
    }

    protected function addAssassinPlayer(): void
    {
        $gameRoleBagBuilder = ThereIs::aStory(GameRoleInitializedStory::class)
            ->execute()
            ->getState(GameRoleInitializedStory::GAME_ROLE_BAG);
        \assert($gameRoleBagBuilder instanceof GameRoleBuilderBag);

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $userBuilder = ThereIs::anUser()
            ->withUsername(\sprintf('%sslipman-13', $this->getPrefix()))
            ->withEmail(\sprintf('%sslipman-13@garoloup.com', $this->getPrefix()));
        $playerBuilder = ThereIs::aPlayer()
            ->withUser($userBuilder)
            ->withRole($gameRoleBagBuilder->getAssassin());

        $this->addState(self::ASSASSIN_USER, $userBuilder, self::TEMP_USERS_POOL);
        $this->addState(self::ASSASSIN, $playerBuilder, self::TEMP_PLAYERS_POOL);

        $gameBuilder->withPlayer($playerBuilder);
    }
}
