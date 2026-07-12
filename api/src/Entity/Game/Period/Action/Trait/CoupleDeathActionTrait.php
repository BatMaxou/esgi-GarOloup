<?php

namespace App\Entity\Game\Period\Action\Trait;

use App\Enum\Game\GameActionTypeEnum;
use Doctrine\ORM\Mapping as ORM;

trait CoupleDeathActionTrait
{
    #[ORM\Column(length: 36)]
    private string $targetPlayerId;

    #[ORM\Column(length: 36)]
    private string $secondaryPlayerId;

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
        return GameActionTypeEnum::COUPLE_DEATH;
    }
}
