<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Vote;

use App\Domain\Workflow\DayOrchestrator;
use App\Domain\Workflow\DayWorkflowComposer;
use App\Domain\Workflow\NightOrchestrator;
use App\Domain\Workflow\NightWorkflowComposer;
use App\Domain\Workflow\VoteResolver;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\ClockInterface;

class ComplexGameVote1HunterInterruptStory extends ComplexGameVote1HunterEliminatedStory
{
    public function __construct(
        NightWorkflowComposer $nightWorkflowComposer,
        DayWorkflowComposer $dayWorkflowComposer,
        ClockInterface $clock,
        EntityManagerInterface $em,
        int $setupDuration,
        NightOrchestrator $nightOrchestrator,
        DayOrchestrator $dayOrchestrator,
        VoteResolver $voteResolver,
        private readonly int $hunterStepDuration,
    ) {
        parent::__construct(
            $nightWorkflowComposer,
            $dayWorkflowComposer,
            $clock,
            $em,
            $setupDuration,
            $nightOrchestrator,
            $dayOrchestrator,
            $voteResolver,
        );
    }

    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $game->setInterruptedRuntimeStep($game->getRuntimeStep());
        $game->setRuntimeStep(GameRuntimeStepEnum::INTERUPT);
        $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $this->hunterStepDuration)));

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-vote-1-hunter-interrupt-';
    }
}
