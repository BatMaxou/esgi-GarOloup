<?php

namespace App\Service\Game;

use App\Api\Model\Game\WerewolfTeam;
use App\Entity\Game\Game;
use App\Repository\Game\PlayerRepository;

class WerewolfTeamFactory
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public function fromGame(Game $game): ?WerewolfTeam
    {
        $gameId = $game->getId();
        if (!$gameId) {
            return null;
        }

        return new WerewolfTeam((string) $gameId, $this->playerRepository->findWerewolvesByGame($game));
    }
}
