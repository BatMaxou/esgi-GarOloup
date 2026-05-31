<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Entity\Game\Period\Action\NightAction\MurderAction;
use App\Entity\Game\Role\WerewolfRole;
use App\Enum\Game\GameRoleEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ClassicGameNight1WerewolfVotedStory extends ClassicGameSetupedStory
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $villagerPlayerBuilder = $this->getState(self::VILLAGER_2);
        \assert($villagerPlayerBuilder instanceof PlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        \assert(null !== $villagerPlayerId);
        $targetPlayerId = $villagerPlayerId->toString();

        foreach ($this->getPool(self::WEREWOLVES_POOL) as $werewolfPlayerBuilder) {
            \assert($werewolfPlayerBuilder instanceof PlayerBuilder);
            $werewolfRole = $werewolfPlayerBuilder->getEntity()->getRole();
            \assert($werewolfRole instanceof WerewolfRole);
            $werewolfRole->setTargetPlayerId($targetPlayerId);
        }

        $night = $game->getCurrentNight() ?? throw new \LogicException('No active night to register the murder');
        $night->addAction(new MurderAction($night, GameRoleEnum::WEREWOLF, $targetPlayerId));

        $this->nightOrchestrator->advance($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-game-night-1-werewolf-voted-';
    }
}
