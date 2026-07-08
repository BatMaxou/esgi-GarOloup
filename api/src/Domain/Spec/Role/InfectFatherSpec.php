<?php

namespace App\Domain\Spec\Role;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction\MurderAction;
use App\Entity\Game\Player;
use App\Entity\Game\Role\InfectFatherRole;
use App\Entity\Game\Role\Interface\NightKillImmuneInterface;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
use Symfony\Component\Clock\ClockInterface;

class InfectFatherSpec
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function canInfect(Player $actor, Game $game): bool
    {
        if (GameRuntimeStepEnum::NIGHT !== $game->getRuntimeStep()) {
            return false;
        }

        if ($game->getStepEndAt() < $this->clock->now()) {
            return false;
        }

        $workflow = $game->getNightWorkflow();
        if (null === $workflow || !\in_array(GameRoleEnum::INFECT_FATHER, $workflow->getCurrentTurn(), true)) {
            return false;
        }

        if ($actor->isDead() || GameTeamEnum::WEREWOLF !== $actor->getTeam()) {
            return false;
        }

        $role = $actor->getRoleAs(InfectFatherRole::class);
        if (!$role instanceof InfectFatherRole || !$role->isInfectionAvailable()) {
            return false;
        }

        $victimId = $this->findWerewolfVictimId($game);
        if (null === $victimId) {
            return false;
        }

        return !$this->isVictimImmune($game, $victimId);
    }

    public function findWerewolfVictimId(Game $game): ?string
    {
        $night = $game->getCurrentNight();
        if (null === $night) {
            return null;
        }

        foreach ($night->getActions() as $action) {
            if ($action instanceof MurderAction && GameRoleEnum::WEREWOLF === $action->getSource()) {
                return $action->getTargetPlayerId();
            }
        }

        return null;
    }

    private function isVictimImmune(Game $game, string $victimId): bool
    {
        foreach ($game->getPlayers() as $player) {
            if ((string) $player->getId() === $victimId) {
                $immuneRole = $player->getRoleAs(NightKillImmuneInterface::class);

                return null !== $immuneRole && $immuneRole->isImmuneToNightMurder(GameRoleEnum::WEREWOLF);
            }
        }

        return false;
    }
}
