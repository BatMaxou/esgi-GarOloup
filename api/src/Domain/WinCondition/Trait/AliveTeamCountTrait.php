<?php

namespace App\Domain\WinCondition\Trait;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;

trait AliveTeamCountTrait
{
    protected function countAlive(Game $game, GameTeamEnum $team): int
    {
        return \count(\array_filter(
            $game->getPlayers()->toArray(),
            fn (Player $player) => !$player->isDead() && $team === $player->getTeam(),
        ));
    }

    protected function hasNoLoneTeamAlive(Game $game): bool
    {
        return 0 === $this->countAlive($game, GameTeamEnum::SOLO)
            && 0 === $this->countAlive($game, GameTeamEnum::COUPLE);
    }

    protected function isRunning(Game $game): bool
    {
        $runtimeStep = $game->getRuntimeStep();

        return null !== $runtimeStep && GameRuntimeStepEnum::FINISH !== $runtimeStep;
    }
}
