<?php

namespace App\Entity\Game\Period\Action\InterruptAction;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\InterruptAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Interface\TargetableActionInterface;
use App\Entity\Game\Period\Interrupt;
use App\Enum\Game\GameActionTypeEnum;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class HunterShotAction extends InterruptAction implements TargetableActionInterface
{
    #[ORM\Column(length: 36)]
    private string $targetPlayerId;

    public function __construct(Interrupt $interrupt, string $targetPlayerId)
    {
        parent::__construct($interrupt, GameRoleEnum::HUNTER);
        $this->targetPlayerId = $targetPlayerId;
    }

    public function getTargetPlayerId(): string
    {
        return $this->targetPlayerId;
    }

    public function getType(): GameActionTypeEnum
    {
        return GameActionTypeEnum::HUNTER_SHOT;
    }

    public function apply(PeriodInterface $period, Game $game): void
    {
        // The hunter's kill is already applied by the HunterShootApplicator; this only records it.
    }
}
