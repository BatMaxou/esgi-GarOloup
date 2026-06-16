<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Night\Hunter;

use App\Entity\Game\Role\WerewolfRole;
use App\Enum\Game\GameRoleEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Seer\ComplexGameNight1SeerRevealedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameNight1WerewolfKilledHunterStory extends ComplexGameNight1SeerRevealedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $hunterPlayerBuilder = $this->getState(self::HUNTER);
        \assert($hunterPlayerBuilder instanceof PlayerBuilder);
        $hunterPlayerId = $hunterPlayerBuilder->getEntity()->getId();
        \assert(null !== $hunterPlayerId);
        $targetPlayerId = $hunterPlayerId->toString();

        foreach ($this->getPool(self::WEREWOLVES_POOL) as $werewolfPlayerBuilder) {
            \assert($werewolfPlayerBuilder instanceof PlayerBuilder);
            $werewolfRole = $werewolfPlayerBuilder->getEntity()->getRole();
            \assert($werewolfRole instanceof WerewolfRole);
            $werewolfRole->setTargetPlayerId($targetPlayerId);
        }

        ThereIs::aMurderAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::WEREWOLF)
            ->against($hunterPlayerBuilder)
            ->build();

        $this->nightOrchestrator->advance($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-night-1-werewolf-killed-hunter-';
    }
}
