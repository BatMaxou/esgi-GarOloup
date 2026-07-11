<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Entity\Game\Period\Action\NightAction\RevealAction;
use App\Entity\Game\Role\SeerRole;
use App\Entity\Game\Role\WerewolfRole;
use App\Enum\Game\GameRoleEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameAssassinNight1WitchTurnStory extends ComplexGameAssassinSetupedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $seerPlayerBuilder = $this->getState(self::SEER);
        \assert($seerPlayerBuilder instanceof PlayerBuilder);
        $seerRole = $seerPlayerBuilder->getEntity()->getRole();
        \assert($seerRole instanceof SeerRole);
        $werewolf1PlayerBuilder = $this->getState(self::WEREWOLF_1);
        \assert($werewolf1PlayerBuilder instanceof PlayerBuilder);
        $seerRole->observe($werewolf1PlayerBuilder->getEntity());

        $night = $game->getCurrentNight() ?? throw new \LogicException('No active night to register the reveal');
        $night->addAction(new RevealAction($night, (string) $werewolf1PlayerBuilder->getEntity()->getId()));

        $this->nightOrchestrator->advance($game);

        $victimPlayerBuilder = $this->getState(self::VILLAGER_4);
        \assert($victimPlayerBuilder instanceof PlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId()?->toString()
            ?? throw new \LogicException('Target player id should not be null');

        foreach ($this->getPool(self::WEREWOLVES_POOL) as $werewolfPlayerBuilder) {
            \assert($werewolfPlayerBuilder instanceof PlayerBuilder);
            $werewolfRole = $werewolfPlayerBuilder->getEntity()->getRole();
            \assert($werewolfRole instanceof WerewolfRole);
            $werewolfRole->setTargetPlayerId($victimPlayerId);
        }

        ThereIs::aMurderAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::WEREWOLF)
            ->against($victimPlayerBuilder)
            ->build();

        $this->nightOrchestrator->advance($game);

        $this->nightOrchestrator->advance($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-assassin-night-1-witch-turn-';
    }
}
