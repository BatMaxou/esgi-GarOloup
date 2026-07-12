<?php

namespace App\Entity\Game\Period\Action\NightAction;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction;
use App\Entity\Game\Period\Action\Trait\CoupleDeathActionTrait;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Interface\SecondaryTargetableActionInterface;
use App\Entity\Game\Period\Interface\TargetableActionInterface;
use App\Entity\Game\Period\Night;
use App\Enum\Game\GameTeamEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'couple_death_night_action')]
class CoupleDeathAction extends NightAction implements TargetableActionInterface, SecondaryTargetableActionInterface
{
    use CoupleDeathActionTrait;

    public function __construct(Night $night, string $targetPlayerId, string $secondaryPlayerId)
    {
        parent::__construct($night, GameTeamEnum::COUPLE);
        $this->targetPlayerId = $targetPlayerId;
        $this->secondaryPlayerId = $secondaryPlayerId;
    }

    public function apply(PeriodInterface $period, Game $game): void
    {
        // The grieving death is already applied by the CoupleChainDeathApplicator; this only records it.
    }
}
