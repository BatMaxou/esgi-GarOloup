<?php

namespace App\Fixtures\Story\ClassicWitchGame;

use App\Tests\Helper\Builder\Game\GameBuilder;

/**
 * Nuit 2 : la sorcière n'utilise aucune potion, son tour s'écoule simplement
 * et la nuit se résout (le villageois ciblé par les loups meurt).
 */
class ClassicWitchGameNight2WitchPassedStory extends ClassicWitchGameNight2WerewolfVotedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $this->nightOrchestrator->advance($gameBuilder->getEntity());

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-witch-game-night-2-witch-passed-';
    }
}
