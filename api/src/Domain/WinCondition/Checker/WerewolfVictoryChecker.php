<?php

namespace App\Domain\WinCondition\Checker;

use App\Domain\WinCondition\Interface\WinConditionCheckerInterface;
use App\Domain\WinCondition\Trait\AliveTeamCountTrait;
use App\Domain\WinCondition\WinResult;
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
        $villagers = $this->countAlive($game, GameTeamEnum::VILLAGE);

        return $werewolves >= 1
            && $werewolves >= $villagers
            && $this->hasNoLoneTeamAlive($game);
    }

    public function getWinResult(): WinResult
    {
        return new WinResult(GameTeamEnum::WEREWOLF);
    }

    public static function getPriority(): int
    {
        return self::DEFAULT_PRIORITY;
    }
}
