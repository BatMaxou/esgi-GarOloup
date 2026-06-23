<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Night\Witch;

use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf\ComplexGameNight1WerewolfVotedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameNight1WitchSavedStory extends ComplexGameNight1WerewolfVotedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->nightOrchestrator->advance($game);

        $witchPlayerBuilder = $this->getState(self::WITCH);
        \assert($witchPlayerBuilder instanceof PlayerBuilder);
        $witchRole = $witchPlayerBuilder->getEntity()->getRole();
        \assert($witchRole instanceof WitchRole);

        $victimPlayerBuilder = $this->getState(self::SEER);
        \assert($victimPlayerBuilder instanceof PlayerBuilder);

        ThereIs::aSaveAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::WITCH)
            ->against($victimPlayerBuilder)
            ->build();

        $witchRole->useHealPotion();

        $this->nightOrchestrator->advance($game);
        $this->dayOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-night-1-witch-saved-';
    }
}
