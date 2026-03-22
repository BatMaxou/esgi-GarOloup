<?php

namespace App\Domain\Spec;

use App\Entity\Game;
use App\Entity\User\AbstractUser;
use App\Entity\User\TempUser;
use App\Enum\GameStepEnum;
use App\Repository\PlayerRepository;

class GameSpec
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public function canCreate(AbstractUser $user): bool
    {
        $players = $this->playerRepository->findByUser($user);
        if (0 === \count($players)) {
            return true;
        }

        foreach ($players as $player) {
            if (!$player->isDead()) {
                return false;
            }
        }

        return true;
    }

    public function canJoin(AbstractUser $user): bool
    {
        $players = $this->playerRepository->findByUser($user);
        if (0 === \count($players)) {
            return true;
        }

        foreach ($players as $player) {
            if (!$player->isDead()) {
                return false;
            }
        }

        return true;
    }

    public function isUsernameAvailable(TempUser $user, Game $game): bool
    {
        $requestedUsername = $user->getUsername();
        foreach ($game->getPlayers() as $player) {
            if ($player->getLinkedUser() === $user) {
                continue;
            }

            if ($requestedUsername === $player->getLinkedUser()->getUsername()) {
                return false;
            }
        }

        return true;
    }

    public function canCloseGameInvitation(AbstractUser $user, Game $game): bool
    {
        $host = $game->getHost();
        if ($user !== $host->getUser() && $user !== $host->getTempUser()) {
            return false;
        }

        return GameStepEnum::NEW === $game->getStep();
    }

    public function canReOpenGameInvitation(AbstractUser $user, Game $game): bool
    {
        $host = $game->getHost();
        if ($user !== $host->getUser() && $user !== $host->getTempUser()) {
            return false;
        }

        return GameStepEnum::CONFIGURATION === $game->getStep();
    }
}
