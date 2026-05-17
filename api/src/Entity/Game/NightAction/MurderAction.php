<?php

namespace App\Entity\Game\NightAction;

use App\Entity\Game\Game;
use App\Entity\Game\Night;
use App\Repository\Game\NightAction\MurderActionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MurderActionRepository::class)]
class MurderAction extends NightAction
{
    #[ORM\Column(length: 36)]
    private string $targetPlayerId;

    public function __construct(Night $night, \App\Enum\Game\GameRoleEnum $source, string $targetPlayerId)
    {
        parent::__construct($night, $source);
        $this->targetPlayerId = $targetPlayerId;
    }

    public function getTargetPlayerId(): string
    {
        return $this->targetPlayerId;
    }

    public function apply(Night $night, Game $game): void
    {
        foreach ($game->getPlayers() as $player) {
            if ((string) $player->getId() === $this->targetPlayerId) {
                $player->setDead(true);

                return;
            }
        }
    }
}
