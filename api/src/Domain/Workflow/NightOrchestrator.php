<?php

namespace App\Domain\Workflow;

use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Domain\Workflow\Interface\PeriodOrchestratorInterface;
use App\Domain\Workflow\TurnRule\TurnValidator;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Night;
use App\Entity\Game\Workflow;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class NightOrchestrator implements PeriodOrchestratorInterface
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly int $nightStepDuration,
        private readonly TurnValidator $turnValidator,
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

        $workflow->reset();
        $this->checkTurnValidity($game, $workflow);

        $game->setRuntimeStep(GameRuntimeStepEnum::NIGHT);
        $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $this->nightStepDuration)));

        if ($workflow->isCompleted()) {
            $this->resolve($game);
        }

        return $night;
    }

    public function advance(Game $game): Game
    {
        $workflow = $game->getNightWorkflow() ?? throw new \LogicException('Workflow missing');
        $workflow->nextStep();

        if ($workflow->isCompleted()) {
            return $this->resolve($game);
        }

        $this->checkTurnValidity($game, $workflow);

        return $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $this->nightStepDuration)));
    }

    private function resolve(Game $game): Game
    {
        $night = $game->getCurrentNight() ?? throw new \LogicException('No active night to resolve');
        foreach ($night->getActions() as $action) {
            $action->apply($night, $game);
        }

        $night->setResolved(true);

        return $game;
    }

    private function checkTurnValidity(Game $game, Workflow $workflow): void
    {
        $shouldPlay = false;
        foreach ($workflow->getCurrentTurn() as $role) {
            if ($this->turnValidator->shouldPlay($game, $role)) {
                $shouldPlay = true;

                break;
            }
        }

        if (!$shouldPlay) {
            $this->advance($game);
        }
    }
}
