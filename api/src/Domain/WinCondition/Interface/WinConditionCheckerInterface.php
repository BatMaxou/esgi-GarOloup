<?php

namespace App\Domain\WinCondition\Interface;

use App\Domain\WinCondition\WinResult;
use App\Entity\Game\Game;

interface WinConditionCheckerInterface
{
    public const int HIGH_PRIORITY = 0;
    public const int DEFAULT_PRIORITY = 50;
    public const int LOW_PRIORITY = 100;

    public function supports(Game $game): bool;

    public function isMet(Game $game): bool;

    public function getWinResult(): WinResult;

    public static function getPriority(): int;
}
