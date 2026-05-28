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
        private readonly int $voteDuration,
    ) {
    }

    public function start(Game $game): Day
    {
        $day = new Day($game, $game->getDays()->count() + 1);
        $game->addDay($day);

        $game->setRuntimeStep(GameRuntimeStepEnum::DAY);
        $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $game->getMaxTimeForDiscussion())));

        return $day;
    }

    public function advance(Game $game): Game
    {
        $workflow = $game->getDayWorkflow() ?? throw new \LogicException('Workflow missing');
        $workflow->nextStep();

        if ($workflow->isCompleted()) {
            $day = $game->getCurrentDay() ?? throw new \LogicException('No active night to resolve');
            $this->resolve($game, $day);

            return $game;
        }

        $workflow->setCurrentTurn($workflow->getStepAt($workflow->getCurrent()));

        return $game;
    }

    public function resolve(Game $game, Day $day): Game
    {
        foreach ($day->getActions() as $action) {
            $action->apply($day, $game);
        }

        $day->setResolved(true);

        // service pour gérer le vote ?
        $game
            ->setRuntimeStep(GameRuntimeStepEnum::VOTE)
            ->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $this->voteDuration)));

        return $game;
    }
}
