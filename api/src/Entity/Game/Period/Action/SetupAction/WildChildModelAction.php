<?php

namespace App\Entity\Game\Period\Action\SetupAction;

use App\Entity\Game\Period\Action\SetupAction;
use App\Entity\Game\Period\Interface\TargetableActionInterface;
use App\Entity\Game\Period\Setup;
use App\Enum\Game\GameActionTypeEnum;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WildChildModelAction extends SetupAction implements TargetableActionInterface
{
    #[ORM\Column(length: 36)]
    private string $targetPlayerId;

    public function __construct(Setup $setup, string $targetPlayerId)
    {
        parent::__construct($setup, GameRoleEnum::WILD_CHILD);
        $this->targetPlayerId = $targetPlayerId;
    }

    public function getTargetPlayerId(): string
    {
        return $this->targetPlayerId;
    }

    public function getType(): GameActionTypeEnum
    {
        return GameActionTypeEnum::WILD_CHILD_MODEL;
    }
}
