<?php

namespace App\Entity\Game\Period\Action\SetupAction;

use App\Entity\Game\Period\Action\SetupAction;
use App\Entity\Game\Period\Interface\SecondaryTargetableActionInterface;
use App\Entity\Game\Period\Interface\TargetableActionInterface;
use App\Entity\Game\Period\Setup;
use App\Enum\Game\GameActionTypeEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CoupleAction extends SetupAction implements TargetableActionInterface, SecondaryTargetableActionInterface
{
    #[ORM\Column(length: 36)]
    private string $targetPlayerId;

    #[ORM\Column(length: 36)]
    private string $secondaryPlayerId;

    public function __construct(Setup $setup, string $firstLoverId, string $secondLoverId)
    {
        parent::__construct($setup);
        $this->targetPlayerId = $firstLoverId;
        $this->secondaryPlayerId = $secondLoverId;
    }

    public function getTargetPlayerId(): string
    {
        return $this->targetPlayerId;
    }

    public function getSecondaryPlayerId(): string
    {
        return $this->secondaryPlayerId;
    }

    public function getType(): GameActionTypeEnum
    {
        return GameActionTypeEnum::COUPLE;
    }
}
