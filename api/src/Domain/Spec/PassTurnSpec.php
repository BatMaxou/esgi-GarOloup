<?php

namespace App\Domain\Spec;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\Interface\PassableRoleInterface;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class PassTurnSpec
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function canPass(Player $player, Game $game): bool
    {
        if (GameRuntimeStepEnum::NIGHT !== $game->getRuntimeStep()) {
            return false;
        }

        if ($game->getStepEndAt() < $this->clock->now()) {
            return false;
        }

        if ($player->isDead()) {
            return false;
        }

        $role = $player->getRoleAs(PassableRoleInterface::class);
        if (null === $role) {
            return false;
        }

        $type = $role->getType();
        if (null === $type) {
            return false;
        }

        $workflow = $game->getNightWorkflow();

        return null !== $workflow && \in_array($type, $workflow->getCurrentTurn(), true);
    }
}
