<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Domain\Workflow\Interface\PeriodOrchestratorInterface;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Night;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class NightOrchestrator implements PeriodOrchestratorInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly DayOrchestrator $dayOrchestrator,
        private readonly int $nightStepDuration,
    ) {
    }

    public function start(Game $game): Night
    {
        $workflow = $game->getNightWorkflow() ?? throw new \LogicException('Workflow missing');

        $night = new Night($game, $game->getNights()->count() + 1);
        $game->addNight($night);

        foreach ($game->getPlayers() as $player) {
            $role = $player->getRole();
            if ($role instanceof NightResettableInterface) {
                $role->clearNightState();
            }
        }

        $workflow->resetForNight();
        $game->setRuntimeStep(GameRuntimeStepEnum::NIGHT);
        $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $this->nightStepDuration)));

        if ($workflow->isCompleted()) {
            $this->resolve($game, $night);
        }

        return $night;
    }

    public function advance(Game $game): Game
    {
        $workflow = $game->getNightWorkflow() ?? throw new \LogicException('Workflow missing');
        $workflow->nextStep();

        if ($workflow->isCompleted()) {
            $night = $game->getCurrentNight() ?? throw new \LogicException('No active night to resolve');
            $this->resolve($game, $night);

            return $game;
        }

        $workflow->setCurrentTurn($workflow->getStepAt($workflow->getCurrent()));

        return $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $this->nightStepDuration)));
    }

    private function resolve(Game $game, Night $night): Game
    {
        foreach ($night->getActions() as $action) {
            $action->apply($night, $game);
        }

        $night->setResolved(true);
        $this->dayOrchestrator->start($game);

        return $game;
    }
}
