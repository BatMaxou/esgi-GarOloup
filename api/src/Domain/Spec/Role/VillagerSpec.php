<?php

namespace App\Domain\Spec\Role;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\VillagerRole;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class VillagerSpec
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function canChooseFriend(Player $player, Game $game, string $targetPlayerId): bool
    {
        if (
            GameRuntimeStepEnum::SETUP !== $game->getRuntimeStep()
            || $game->getStepEndAt() < $this->clock->now()
            || $player->getId()?->toString() === $targetPlayerId
        ) {
            return false;
        }

        $targetExists = false;
        foreach ($game->getPlayers() as $candidate) {
            if ($candidate->getId()?->toString() === $targetPlayerId) {
                $targetExists = true;
            }
        }

        return $targetExists && $player->getRole() instanceof VillagerRole;
    }
}
