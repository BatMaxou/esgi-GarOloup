<?php

namespace App\Fixtures\Story\ClassicWitchGame;

use App\Entity\Game\Role\WerewolfRole;
use App\Enum\Game\GameRoleEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ClassicWitchGameNight1WerewolfVotedStory extends ClassicWitchGameNight1SeerRevealedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $victimPlayerBuilder = $this->getState(self::SEER);
        \assert($victimPlayerBuilder instanceof PlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId();
        \assert(null !== $victimPlayerId);
        $targetPlayerId = $victimPlayerId->toString();

        foreach ($this->getPool(self::WEREWOLVES_POOL) as $werewolfPlayerBuilder) {
            \assert($werewolfPlayerBuilder instanceof PlayerBuilder);
            $werewolfRole = $werewolfPlayerBuilder->getEntity()->getRole();
            \assert($werewolfRole instanceof WerewolfRole);
            $werewolfRole->setTargetPlayerId($targetPlayerId);
        }

        ThereIs::aMurderAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::WEREWOLF)
            ->against($victimPlayerBuilder)
            ->build();

        $this->nightOrchestrator->advance($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-witch-game-night-1-werewolf-voted-';
    }
}
