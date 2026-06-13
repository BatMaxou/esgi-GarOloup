<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf;

use App\Entity\Game\Role\WerewolfRole;
use App\Enum\Game\GameRoleEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Seer\ComplexGameNight3SeerRevealedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameNight3WerewolfVotedStory extends ComplexGameNight3SeerRevealedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $villagerPlayerBuilder = $this->getState(self::VILLAGER_3);
        \assert($villagerPlayerBuilder instanceof PlayerBuilder);
        $targetPlayerId = $villagerPlayerBuilder->getEntity()->getId()?->toString()
            ?? throw new \LogicException('Target player id should not be null');

        foreach ($this->getPool(self::WEREWOLVES_POOL) as $werewolfPlayerBuilder) {
            \assert($werewolfPlayerBuilder instanceof PlayerBuilder);
            $werewolfPlayer = $werewolfPlayerBuilder->getEntity();
            if ($werewolfPlayer->isDead()) {
                continue;
            }

            $werewolfRole = $werewolfPlayer->getRole();
            \assert($werewolfRole instanceof WerewolfRole);
            $werewolfRole->setTargetPlayerId($targetPlayerId);
        }

        ThereIs::aMurderAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::WEREWOLF)
            ->against($villagerPlayerBuilder)
            ->build();

        $this->nightOrchestrator->advance($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-night-3-werewolf-voted-';
    }
}
