<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Interface\PeriodOrchestratorInterface;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Day;
use App\Entity\Game\Workflow;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class DayOrchestrator implements PeriodOrchestratorInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function start(Game $game): Day
    {
        $workflow = $game->getDayWorkflow() ?? throw new \LogicException('Workflow missing');

        $day = new Day($game, $game->getDays()->count() + 1);
        $game->addDay($day);

        $workflow->reset();
        $this->checkTurnValidity($game, $workflow);

        $game->setRuntimeStep(GameRuntimeStepEnum::DAY);
        $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $game->getMaxTimeForDiscussion())));

        return $day;
    }

    public function advance(Game $game): Game
    {
        $workflow = $game->getDayWorkflow() ?? throw new \LogicException('Workflow missing');
        $workflow->nextStep();

        if ($workflow->isCompleted()) {
            return $this->resolve($game);
        }

        $this->checkTurnValidity($game, $workflow);

        return $game;
    }

    public function resolve(Game $game): Game
    {
        $day = $game->getCurrentDay() ?? throw new \LogicException('No active day to resolve');
        foreach ($day->getActions() as $action) {
            $action->apply($day, $game);
        }

        $day->setResolved(true);

        return $game;
    }

    private function checkTurnValidity(Game $game, Workflow $workflow): void
    {
        $currentTurn = $workflow->getCurrentTurn();
        if (empty($currentTurn)) {
            return;
        }

        $pass = true;
        foreach ($currentTurn as $role) {
            if ($player = $game->getPlayer($role)) {
                $pass = $pass && $player->isDead();
            }
        }

        if ($pass) {
            $this->advance($game);
        }
    }
}
