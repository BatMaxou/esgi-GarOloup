<?php

namespace App\Domain\Spec\Role;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction\InfectAction;
use App\Entity\Game\Period\Action\NightAction\MurderAction;
use App\Entity\Game\Player;
use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class WitchSpec
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function canSave(Player $witch, Game $game, Player $target): bool
    {
        if (!$this->isWitchTurn($witch, $game)) {
            return false;
        }

        $role = $witch->getRoleAs(WitchRole::class);
        if (!$role instanceof WitchRole || !$role->isHealPotionAvailable() || $role->hasActedThisNight()) {
            return false;
        }

        if ($target->getGame() !== $game) {
            return false;
        }

        return $this->isNightMurderVictim($game, $target);
    }

    public function canPoison(Player $witch, Game $game, Player $target): bool
    {
        if (!$this->isWitchTurn($witch, $game)) {
            return false;
        }

        $role = $witch->getRoleAs(WitchRole::class);
        if (!$role instanceof WitchRole || !$role->isPoisonPotionAvailable() || $role->hasActedThisNight()) {
            return false;
        }

        if ($witch->getId()?->toString() === $target->getId()?->toString()) {
            return false;
        }

        if ($target->getGame() !== $game || $target->isDead()) {
            return false;
        }

        return true;
    }

    private function isWitchTurn(Player $witch, Game $game): bool
    {
        if (GameRuntimeStepEnum::NIGHT !== $game->getRuntimeStep()) {
            return false;
        }

        if ($game->getStepEndAt() < $this->clock->now()) {
            return false;
        }

        if ($witch->isDead()) {
            return false;
        }

        $workflow = $game->getNightWorkflow();

        return null !== $workflow && \in_array(GameRoleEnum::WITCH, $workflow->getCurrentTurn(), true);
    }

    private function isNightMurderVictim(Game $game, Player $target): bool
    {
        $night = $game->getCurrentNight();
        if (null === $night) {
            return false;
        }

        $targetId = $target->getId()?->toString();

        foreach ($night->getActions() as $action) {
            if ($action instanceof InfectAction && $action->getTargetPlayerId() === $targetId) {
                return false;
            }
        }

        foreach ($night->getActions() as $action) {
            if ($action instanceof MurderAction && $action->getTargetPlayerId() === $targetId) {
                return true;
            }
        }

        return false;
    }
}
