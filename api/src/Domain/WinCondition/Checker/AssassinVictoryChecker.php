<?php

namespace App\Domain\WinCondition\Checker;

use App\Domain\WinCondition\Interface\WinConditionCheckerInterface;
use App\Domain\WinCondition\Trait\AliveTeamCountTrait;
use App\Domain\WinCondition\WinResult;
use App\Entity\Game\Game;
use App\Entity\Game\Role\LoverRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;

class AssassinVictoryChecker implements WinConditionCheckerInterface
{
    use AliveTeamCountTrait;

    public function supports(Game $game): bool
    {
        return $this->isRunning($game);
    }

    public function isMet(Game $game): bool
    {
        return $this->isAssassinAlive($game)
            && 0 === $this->countAlive($game, GameTeamEnum::VILLAGE)
            && 0 === $this->countAlive($game, GameTeamEnum::WEREWOLF)
            && 0 === $this->countAlive($game, GameTeamEnum::COUPLE);
    }

    public function getWinResult(): WinResult
    {
        return new WinResult(GameTeamEnum::SOLO, GameRoleEnum::ASSASSIN);
    }

    public static function getPriority(): int
    {
        return self::HIGH_PRIORITY;
    }

    private function isAssassinAlive(Game $game): bool
    {
        $assassin = $game->getPlayer(GameRoleEnum::ASSASSIN);

        if (null === $assassin || null !== $assassin->getRoleAs(LoverRole::class)) {
            return false;
        }

        return !$assassin->isDead();
    }
}
