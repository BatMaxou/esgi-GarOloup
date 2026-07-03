<?php

namespace App\Domain\Spec\Role;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\HunterRole;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class HunterSpec
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function canShoot(Player $hunter, Game $game, Player $target): bool
    {
        if (GameRuntimeStepEnum::INTERRUPT !== $game->getRuntimeStep()) {
            return false;
        }

        if ($game->getStepEndAt() < $this->clock->now()) {
            return false;
        }

        $role = $hunter->getRole();
        if (!$role instanceof HunterRole || $role->hasShot()) {
            return false;
        }

        if ($target->isDead()) {
            return false;
        }

        if ($target->getGame() !== $game) {
            return false;
        }

        if ($target->getId()?->toString() === $hunter->getId()?->toString()) {
            return false;
        }

        return true;
    }
}
