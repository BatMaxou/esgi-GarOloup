<?php

namespace App\Entity\Game\Period\Action\InterruptAction;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\InterruptAction;
use App\Entity\Game\Period\Action\Trait\CoupleDeathActionTrait;
use App\Entity\Game\Period\Action\Trait\RevealedRoleActionTrait;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Interface\RevealedRoleActionInterface;
use App\Entity\Game\Period\Interface\SecondaryTargetableActionInterface;
use App\Entity\Game\Period\Interface\TargetableActionInterface;
use App\Entity\Game\Period\Interrupt;
use App\Enum\Game\GameTeamEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'couple_death_interrupt_action')]
class CoupleDeathAction extends InterruptAction implements TargetableActionInterface, SecondaryTargetableActionInterface, RevealedRoleActionInterface
{
    use CoupleDeathActionTrait;
    use RevealedRoleActionTrait;

    public function __construct(Interrupt $interrupt, string $targetPlayerId, string $secondaryPlayerId)
    {
        parent::__construct($interrupt, GameTeamEnum::COUPLE);
        $this->targetPlayerId = $targetPlayerId;
        $this->secondaryPlayerId = $secondaryPlayerId;
    }

    public function apply(PeriodInterface $period, Game $game): void
    {
        // The grieving death is already applied by the CoupleChainDeathApplicator; this only records it.
    }
}
