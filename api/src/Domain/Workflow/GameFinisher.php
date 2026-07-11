<?php

namespace App\Domain\Workflow;

use App\Domain\WinCondition\WinDetector;
use App\Domain\WinCondition\WinResult;
use App\Entity\Game\Game;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Service\Game\RecapFactory;

class GameFinisher
{
    public function __construct(
        private readonly WinDetector $winDetector,
        private readonly RecapFactory $recapFactory,
    ) {
    }

    public function tryFinish(Game $game): bool
    {
        $result = $this->winDetector->detect($game);
        if (null === $result) {
            return false;
        }

        $this->finish($game, $result);

        return true;
    }

    public function finish(Game $game, WinResult $result): void
    {
        $game->setRuntimeStep(GameRuntimeStepEnum::FINISH);
        $game->setStepEndAt(null);
        $game->setWinningTeam($result->team);
        $game->setWinningRole($result->winningRole);

        $this->recapFactory->createFromGame($game);
    }
}
