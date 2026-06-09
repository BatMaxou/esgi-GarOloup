<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Interface\PeriodOrchestratorInterface;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Day;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class DayOrchestrator implements PeriodOrchestratorInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly VoteResolver $voteResolver,
        private readonly GameFinisher $gameFinisher,
    ) {
    }

    public function start(Game $game): Day
    {
        $workflow = $game->getDayWorkflow() ?? throw new \LogicException('Workflow missing');

        $day = new Day($game, $game->getDays()->count() + 1);
        $game->addDay($day);

        $workflow->reset();
        $game->setRuntimeStep(GameRuntimeStepEnum::DAY);
        $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $game->getMaxTimeForDiscussion())));

        return $day;
    }

    public function advance(Game $game): Game
    {
        $workflow = $game->getDayWorkflow() ?? throw new \LogicException('Workflow missing');
        $workflow->nextStep();

        if ($workflow->isCompleted()) {
            $this->resolve($game);

            return $game;
        }

        $workflow->setCurrentTurn($workflow->getStepAt($workflow->getCurrent()));

        return $game;
    }

    public function resolve(Game $game): Game
    {
        $day = $game->getCurrentDay() ?? throw new \LogicException('No active day to resolve');
        foreach ($day->getActions() as $action) {
            $action->apply($day, $game);
        }

        $day->setResolved(true);

        if ($this->gameFinisher->tryFinish($game)) {
            return $game;
        }

        $this->voteResolver->start($game);

        return $game;
    }
}
