<?php

namespace App\Domain\Spec\Role;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\WerewolfRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class WerewolfSpec
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function canSeeTeam(Player $player, Game $game): bool
    {
        return $game->getRuntimeStep() && $player->getRole() instanceof WerewolfRole;
    }

    public function canVote(Player $voter, Game $game, Player $targetPlayer): bool
    {
        if (GameRuntimeStepEnum::NIGHT !== $game->getRuntimeStep()) {
            return false;
        }

        if ($game->getStepEndAt() < $this->clock->now()) {
            return false;
        }

        $workflow = $game->getNightWorkflow();
        if (null === $workflow || !\in_array(GameRoleEnum::WEREWOLF, $workflow->getCurrentTurn(), true)) {
            return false;
        }

        if (!$voter->getRole() instanceof WerewolfRole || $voter->isDead()) {
            return false;
        }

        if ($voter->getId()?->toString() === $targetPlayer->getId()?->toString()) {
            return false;
        }

        if ($targetPlayer->isDead()) {
            return false;
        }

        if ($targetPlayer->getRole() instanceof WerewolfRole) {
            return false;
        }

        return true;
    }
}
