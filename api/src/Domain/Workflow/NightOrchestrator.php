<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Entity\Game\Game;
use App\Entity\Game\Night;
use App\Enum\Game\GameRuntimeStepEnum;

class NightOrchestrator
{
    public function __construct(
        private readonly int $nightStepDuration,
    ) {
    }

    public function startNight(Game $game): void
    {
        $workflow = $game->getWorkflow() ?? throw new \LogicException('Workflow missing');

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
        $game->setStepEndAt(new \DateTimeImmutable(\sprintf('+%d seconds', $this->nightStepDuration)));

        if ($workflow->isCompleted()) {
            $this->resolve($game, $night);
        }
    }

    public function advance(Game $game): void
    {
        $workflow = $game->getWorkflow() ?? throw new \LogicException('Workflow missing');
        $workflow->nextStep();

        if ($workflow->isCompleted()) {
            $night = $this->findCurrentNight($game)
                ?? throw new \LogicException('No active night to resolve');
            $this->resolve($game, $night);

            return;
        }

        $workflow->setCurrentTurn($workflow->getStepAt($workflow->getCurrent()));
        $game->setStepEndAt(new \DateTimeImmutable(\sprintf('+%d seconds', $this->nightStepDuration)));
    }

    private function resolve(Game $game, Night $night): void
    {
        foreach ($night->getActions() as $action) {
            $action->apply($night, $game);
        }

        $night->setResolved(true);
        $game->setRuntimeStep(GameRuntimeStepEnum::DAY);
        $game->setStepEndAt(new \DateTimeImmutable(\sprintf('+%d seconds', $game->getMaxTimeForDiscussion())));
    }

    private function findCurrentNight(Game $game): ?Night
    {
        foreach ($game->getNights() as $night) {
            if (!$night->isResolved()) {
                return $night;
            }
        }

        return null;
    }
}
