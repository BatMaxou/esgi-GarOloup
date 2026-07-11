<?php

namespace App\Fixtures\Story\ClassicGame\Runtime\Night\Seer;

use App\Entity\Game\Period\Action\NightAction\RevealAction;
use App\Entity\Game\Role\SeerRole;
use App\Fixtures\Story\ClassicGame\Runtime\Vote\ClassicGameVote1ResolvedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

class ClassicGameNight2SeerRevealedStory extends ClassicGameVote1ResolvedStory
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

        $werewolf2PlayerBuilder = $this->getState(self::WEREWOLF_2);
        \assert($werewolf2PlayerBuilder instanceof PlayerBuilder);

        $seerRole->observe($werewolf2PlayerBuilder->getEntity());

        $night = $game->getCurrentNight() ?? throw new \LogicException('No active night to register the reveal');
        $night->addAction(new RevealAction($night, (string) $werewolf2PlayerBuilder->getEntity()->getId()));

        $this->nightOrchestrator->advance($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-game-night-2-seer-revealed-';
    }
}
