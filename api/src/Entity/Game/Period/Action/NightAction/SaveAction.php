<?php

namespace App\Entity\Game\Period\Action\NightAction;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Interface\TargetableActionInterface;
use App\Entity\Game\Period\Night;
use App\Enum\Game\GameActionTypeEnum;
use App\Enum\Game\GameRoleEnum;
use App\Repository\Game\Period\Action\NightAction\SaveActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaveActionRepository::class)]
class SaveAction extends NightAction implements TargetableActionInterface
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
                $player->setDead(false);

                return;
            }
        }
    }

    public function getType(): GameActionTypeEnum
    {
        return GameActionTypeEnum::SAVE;
    }
}
