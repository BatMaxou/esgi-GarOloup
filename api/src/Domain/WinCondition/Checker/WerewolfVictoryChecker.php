<?php

namespace App\Domain\WinCondition\Checker;

use App\Domain\WinCondition\Interface\WinConditionCheckerInterface;
use App\Domain\WinCondition\Trait\AliveTeamCountTrait;
use App\Entity\Game\Game;
use App\Enum\Game\GameTeamEnum;

class WerewolfVictoryChecker implements WinConditionCheckerInterface
{
    use AliveTeamCountTrait;

    public function supports(Game $game): bool
    {
        return $this->isRunning($game);
    }

    public function isMet(Game $game): bool
    {
        $werewolves = $this->countAlive($game, GameTeamEnum::WEREWOLF);

        return $werewolves >= 1
            && $werewolves >= $this->countAlive($game, GameTeamEnum::VILLAGE)
            && $this->hasNoLoneTeamAlive($game);
    }

    public function getWinningTeam(): GameTeamEnum
    {
        return GameTeamEnum::WEREWOLF;
    }

    public static function getPriority(): int
    {
        return self::DEFAULT_PRIORITY;
    }
}
