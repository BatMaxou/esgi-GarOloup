<?php

namespace App\Entity\Game\Period\Interface;

use App\Entity\Game\Game;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;

interface PeriodAction
{
    public function getSource(): GameRoleEnum|GameTeamEnum|null;

    public function apply(PeriodInterface $period, Game $game): void;
}
