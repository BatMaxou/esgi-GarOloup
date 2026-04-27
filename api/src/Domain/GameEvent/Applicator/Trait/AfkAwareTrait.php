<?php

namespace App\Domain\GameEvent\Applicator\Trait;

use App\Entity\Game\Player;

trait AfkAwareTrait
{
    protected function handleAfkPlayer(Player $player): Player
    {
        $player->incrementAfkCount();
        if ($player->getAfkCount() >= $this->getAfkThreshold()) {
            $player->setDead(true);
        }

        return $player;
    }

    abstract protected function getAfkThreshold(): int;
}
