<?php

namespace App\Service\Game\Period;

use App\Entity\Game\Period\Action\NightAction\InfectAction;
use App\Entity\Game\Period\Action\NightAction\MurderAction;
use App\Entity\Game\Period\Action\NightAction\SaveAction;
use App\Entity\Game\Period\Interface\PeriodAction;
use App\Entity\Game\Player;
use App\Enum\Game\GameActionTypeEnum;

class ActionVisibilityResolver
{
    /** @param iterable<PeriodAction> $periodActions */
    public function isMasked(PeriodAction $action, ?Player $currentPlayer, iterable $periodActions): bool
    {
        if (\in_array($action->getType(), [GameActionTypeEnum::SAVE, GameActionTypeEnum::INFECTION, GameActionTypeEnum::REVEAL], true)) {
            return true;
        }

        if ($action instanceof MurderAction && $this->isCancelledBySave($action, $periodActions)) {
            return true;
        }

        if ($action instanceof MurderAction && $this->isCancelledByInfection($action, $periodActions)) {
            return true;
        }

        return false;
    }

    /** @param iterable<PeriodAction> $periodActions */
    private function isCancelledBySave(MurderAction $murder, iterable $periodActions): bool
    {
        foreach ($periodActions as $action) {
            if ($action instanceof SaveAction && $action->getTargetPlayerId() === $murder->getTargetPlayerId()) {
                return true;
            }
        }

        return false;
    }

    /** @param iterable<PeriodAction> $periodActions */
    private function isCancelledByInfection(MurderAction $murder, iterable $periodActions): bool
    {
        foreach ($periodActions as $action) {
            if ($action instanceof InfectAction && $action->getTargetPlayerId() === $murder->getTargetPlayerId()) {
                return true;
            }
        }

        return false;
    }
}
