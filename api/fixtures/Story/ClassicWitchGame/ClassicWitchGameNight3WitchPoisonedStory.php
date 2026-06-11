<?php

namespace App\Fixtures\Story\ClassicWitchGame;

use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

/**
 * Nuit 3 : la sorcière empoisonne le dernier loup. La nuit se résout :
 * le villageois ciblé et le dernier loup meurent, le village l'emporte.
 */
class ClassicWitchGameNight3WitchPoisonedStory extends ClassicWitchGameNight3WerewolfVotedStory
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

        $werewolf3PlayerBuilder = $this->getState(self::WEREWOLF_3);
        \assert($werewolf3PlayerBuilder instanceof PlayerBuilder);

        ThereIs::aMurderAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::WITCH)
            ->against($werewolf3PlayerBuilder)
            ->build();

        $witchRole->usePoisonPotion();

        $this->nightOrchestrator->advance($game);
        $this->dayOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-witch-game-night-3-witch-poisoned-';
    }
}
