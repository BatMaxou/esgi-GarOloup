<?php

namespace App\Fixtures\Story\ClassicWitchGame;

use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ClassicWitchGameNight1WitchSavedStory extends ClassicWitchGameNight1WerewolfVotedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

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

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-witch-game-night-1-witch-saved-';
    }
}
