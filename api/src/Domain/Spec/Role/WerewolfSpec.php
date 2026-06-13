<?php

namespace App\Domain\Spec\Role;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
use Symfony\Component\Clock\ClockInterface;

class WerewolfSpec
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function canSeeTeam(Player $player, Game $game): bool
    {
        return $game->getRuntimeStep() && GameTeamEnum::WEREWOLF === $player->getTeam();
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

        if (GameTeamEnum::WEREWOLF !== $voter->getTeam() || $voter->isDead()) {
            return false;
        }

        if ($voter->getId()?->toString() === $targetPlayer->getId()?->toString()) {
            return false;
        }

        if ($targetPlayer->isDead()) {
            return false;
        }

        return true;
    }
}
