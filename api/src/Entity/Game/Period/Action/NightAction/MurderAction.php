<?php

namespace App\Entity\Game\Period\Action\NightAction;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Night;
use App\Enum\Game\GameActionTypeEnum;
use App\Enum\Game\GameRoleEnum;
use App\Repository\Game\Period\Action\NightAction\MurderActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MurderActionRepository::class)]
class MurderAction extends NightAction
{
    #[ORM\Column(length: 36)]
    private string $targetPlayerId;

    public function __construct(Night $night, GameRoleEnum $source, string $targetPlayerId)
    {
        parent::__construct($night, $source);
        $this->targetPlayerId = $targetPlayerId;
    }

    public function getTargetPlayerId(): string
    {
        return $this->targetPlayerId;
    }

    public function apply(PeriodInterface $period, Game $game): void
    {
        foreach ($game->getPlayers() as $player) {
            if ((string) $player->getId() === $this->targetPlayerId) {
                $player->setDead(true);

                return;
            }
        }
    }

    public function getType(): GameActionTypeEnum
    {
        return GameActionTypeEnum::MURDER;
    }
}
