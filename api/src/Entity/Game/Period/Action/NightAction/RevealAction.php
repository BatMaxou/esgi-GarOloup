<?php

namespace App\Entity\Game\Period\Action\NightAction;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Interface\TargetableActionInterface;
use App\Entity\Game\Period\Night;
use App\Enum\Game\GameActionTypeEnum;
use App\Enum\Game\GameRoleEnum;
use App\Repository\Game\Period\Action\NightAction\RevealActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RevealActionRepository::class)]
class RevealAction extends NightAction implements TargetableActionInterface
{
    #[ORM\Column(length: 36)]
    private string $targetPlayerId;

    public function __construct(Night $night, string $targetPlayerId)
    {
        parent::__construct($night, GameRoleEnum::SEER);
        $this->targetPlayerId = $targetPlayerId;
    }

    public function getTargetPlayerId(): string
    {
        return $this->targetPlayerId;
    }

    public function apply(PeriodInterface $period, Game $game): void
    {
        // A reveal has no effect on the game state; it only records what the seer observed.
    }

    public function getType(): GameActionTypeEnum
    {
        return GameActionTypeEnum::REVEAL;
    }
}
