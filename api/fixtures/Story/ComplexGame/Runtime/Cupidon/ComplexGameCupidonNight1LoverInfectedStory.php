<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Cupidon;

use App\Entity\Game\Role\InfectFatherRole;
use App\Enum\Game\GameRoleEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameCupidonNight1LoverInfectedStory extends ComplexGameCupidonNight1LoverKilledStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $victimPlayerBuilder = $this->getState($this->getFirstLoverState());
        \assert($victimPlayerBuilder instanceof PlayerBuilder);

        $infectFatherPlayerBuilder = $this->getState(self::INFECT_FATHER);
        \assert($infectFatherPlayerBuilder instanceof PlayerBuilder);
        $infectFatherRole = $infectFatherPlayerBuilder->getEntity()->getRole();
        \assert($infectFatherRole instanceof InfectFatherRole);

        ThereIs::anInfectAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::INFECT_FATHER)
            ->against($victimPlayerBuilder)
            ->build();

        $infectFatherRole->useInfection();

        $this->nightOrchestrator->advance($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-cupidon-night-1-lover-infected-';
    }
}
