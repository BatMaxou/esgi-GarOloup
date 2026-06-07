<?php

namespace App\Domain\WinCondition\Checker;

use App\Domain\WinCondition\Interface\WinConditionCheckerInterface;
use App\Domain\WinCondition\Trait\AliveTeamCountTrait;
use App\Entity\Game\Game;
use App\Enum\Game\GameTeamEnum;

class VillageVictoryChecker implements WinConditionCheckerInterface
{
    use AliveTeamCountTrait;

    public function supports(Game $game): bool
    {
        return $this->isRunning($game);
    }

    public function isMet(Game $game): bool
    {
        return 0 === $this->countAlive($game, GameTeamEnum::WEREWOLF)
            && $this->hasNoLoneTeamAlive($game);
    }

    public function getWinningTeam(): GameTeamEnum
    {
        return GameTeamEnum::VILLAGE;
    }

    public static function getPriority(): int
    {
        return self::HIGH_PRIORITY;
    }
}
