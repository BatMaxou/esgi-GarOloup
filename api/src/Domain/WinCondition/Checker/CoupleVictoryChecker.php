<?php

namespace App\Domain\WinCondition\Checker;

use App\Domain\WinCondition\Interface\WinConditionCheckerInterface;
use App\Domain\WinCondition\Trait\AliveTeamCountTrait;
use App\Domain\WinCondition\WinResult;
use App\Entity\Game\Game;
use App\Enum\Game\GameTeamEnum;

class CoupleVictoryChecker implements WinConditionCheckerInterface
{
    use AliveTeamCountTrait;

    public function supports(Game $game): bool
    {
        return $this->isRunning($game);
    }

    public function isMet(Game $game): bool
    {
        return $this->countAlive($game, GameTeamEnum::COUPLE) >= 1
            && 0 === $this->countAlive($game, GameTeamEnum::WEREWOLF)
            && 0 === $this->countAlive($game, GameTeamEnum::VILLAGE)
            && 0 === $this->countAlive($game, GameTeamEnum::SOLO);
    }

    public function getWinResult(): WinResult
    {
        return new WinResult(GameTeamEnum::COUPLE);
    }

    public static function getPriority(): int
    {
        return self::HIGH_PRIORITY;
    }
}
