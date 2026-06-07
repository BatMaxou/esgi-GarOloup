<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Domain\Workflow\DayOrchestrator;
use App\Domain\Workflow\DayWorkflowComposer;
use App\Domain\Workflow\NightOrchestrator;
use App\Domain\Workflow\NightWorkflowComposer;
use App\Domain\Workflow\VoteResolver;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;
use Doctrine\ORM\EntityManagerInterface;

class ClassicGameVote1ResolvedStory extends ClassicGameDay1FinishedStory
{
    public function __construct(
        NightWorkflowComposer $nightWorkflowComposer,
        DayWorkflowComposer $dayWorkflowComposer,
        EntityManagerInterface $em,
        NightOrchestrator $nightOrchestrator,
        DayOrchestrator $dayOrchestrator,
        private readonly VoteResolver $voteResolver,
    ) {
        parent::__construct($nightWorkflowComposer, $dayWorkflowComposer, $em, $nightOrchestrator, $dayOrchestrator);
    }

    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $werewolfPlayerBuilder = $this->getState(self::WEREWOLF_1);
        \assert($werewolfPlayerBuilder instanceof PlayerBuilder);

        foreach ($this->getPool(self::VILLAGERS_POOL) as $villagerPlayerBuilder) {
            \assert($villagerPlayerBuilder instanceof PlayerBuilder);
            if ($villagerPlayerBuilder->getEntity()->isDead()) {
                continue;
            }

            ThereIs::aBallot()
                ->forGame($gameBuilder)
                ->by($villagerPlayerBuilder)
                ->against($werewolfPlayerBuilder)
                ->build();
        }

        $this->voteResolver->resolve($game);
        $this->nightOrchestrator->start($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-game-vote-1-resolved-';
    }
}
