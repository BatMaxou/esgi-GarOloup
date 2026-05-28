<?php

namespace App\Domain\Workflow\Interface;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Interface\PeriodInterface;

interface PeriodOrchestratorInterface
{
    public function start(Game $game): PeriodInterface;

    public function advance(Game $game): Game;
}
