<?php

namespace App\Domain\Workflow;

use App\Domain\WinCondition\WinDetector;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;

class GameFinisher
{
    public function __construct(
        private readonly WinDetector $winDetector,
    ) {
    }

    public function tryFinish(Game $game): bool
    {
        $team = $this->winDetector->detect($game);
        if (null === $team) {
            return false;
        }

        $this->finish($game, $team);

        return true;
    }

    public function finish(Game $game, GameTeamEnum $winningTeam): void
    {
        $game->setRuntimeStep(GameRuntimeStepEnum::FINISH);
        $game->setStepEndAt(null);
        $game->setWinningTeam($winningTeam);
    }
}
