<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

use App\Entity\Game\Role\CupidonRole;
use App\Fixtures\Story\Role\GameRoleInitializedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\Game\Role\GameRoleBuilderBag;
use App\Tests\Helper\ThereIs;

trait CupidonPlayerAwareTrait
{
    public const CUPIDON_USER = 'cupidon_user';
    public const CUPIDON = 'cupidon';

    protected function addCupidonPlayer(): void
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
            ->withRole($gameRoleBagBuilder->getCupidon());

        $this->addState(self::CUPIDON_USER, $userBuilder, self::TEMP_USERS_POOL);
        $this->addState(self::CUPIDON, $playerBuilder, self::TEMP_PLAYERS_POOL);

        $gameBuilder->withPlayer($playerBuilder);
    }

    protected function setupCouple(string $firstLoverState, string $secondLoverState): void
    {
        $firstLoverPlayerBuilder = $this->getState($firstLoverState);
        \assert($firstLoverPlayerBuilder instanceof PlayerBuilder);
        $secondLoverPlayerBuilder = $this->getState($secondLoverState);
        \assert($secondLoverPlayerBuilder instanceof PlayerBuilder);

        $firstLover = $firstLoverPlayerBuilder->getEntity();
        $secondLover = $secondLoverPlayerBuilder->getEntity();
        $firstLoverId = $firstLover->getId()?->toString();
        \assert(null !== $firstLoverId);
        $secondLoverId = $secondLover->getId()?->toString();
        \assert(null !== $secondLoverId);

        $firstLoverOriginalRole = $firstLover->getRole();
        \assert(null !== $firstLoverOriginalRole);
        $secondLoverOriginalRole = $secondLover->getRole();
        \assert(null !== $secondLoverOriginalRole);

        $firstLoverTeam = $firstLover->getTeam();
        $firstLover->setRole(
            ThereIs::aLoverRole()
                ->wrapping($firstLoverOriginalRole)
                ->withPartner($secondLoverId)
                ->build()
                ->getEntity(),
        );
        $firstLover->setTeam($firstLoverTeam);

        $secondLoverTeam = $secondLover->getTeam();
        $secondLover->setRole(
            ThereIs::aLoverRole()
                ->wrapping($secondLoverOriginalRole)
                ->withPartner($firstLoverId)
                ->build()
                ->getEntity(),
        );
        $secondLover->setTeam($secondLoverTeam);

        $cupidonPlayerBuilder = $this->getState(self::CUPIDON);
        \assert($cupidonPlayerBuilder instanceof PlayerBuilder);
        $cupidonRole = $cupidonPlayerBuilder->getEntity()->getRole();
        \assert($cupidonRole instanceof CupidonRole);
        $cupidonRole->setSetup(true);
    }
}
