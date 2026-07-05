<?php

namespace App\Domain\WinCondition\Trait;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\CupidonRole;
use App\Entity\Game\Role\LoverRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;

trait AliveTeamCountTrait
{
    protected function countAlive(Game $game, GameTeamEnum $team): int
    {
        return \count(\array_filter(
            $game->getPlayers()->toArray(),
            fn (Player $player) => !$player->isDead() && $team === $this->getVictoryTeam($player, $game),
        ));
    }

    private function getVictoryTeam(Player $player, Game $game): ?GameTeamEnum
    {
        if (null !== $player->getRoleAs(LoverRole::class)) {
            return GameTeamEnum::COUPLE;
        }

        if (null !== $player->getRoleAs(CupidonRole::class) && $this->areBothLoversAlive($game)) {
            return GameTeamEnum::COUPLE;
        }

        return $player->getTeam();
    }

    private function areBothLoversAlive(Game $game): bool
    {
        $lovers = \array_filter(
            $game->getPlayers()->toArray(),
            fn (Player $player) => null !== $player->getRoleAs(LoverRole::class),
        );

        if (2 !== \count($lovers)) {
            return false;
        }

        foreach ($lovers as $lover) {
            if ($lover->isDead()) {
                return false;
            }
        }

        return true;
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
