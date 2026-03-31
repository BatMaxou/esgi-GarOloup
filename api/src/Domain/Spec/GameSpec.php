<?php

namespace App\Domain\Spec;

use App\Entity\Game\Game;
use App\Entity\User\AbstractUser;
use App\Entity\User\TempUser;
use App\Enum\Game\GameStepEnum;
use App\Repository\Game\PlayerRepository;

class GameSpec
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly int $minimumPlayers,
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
        if ($user !== $game->getHost()->getLinkedUser()) {
            return false;
        }

        if ($game->getPlayers()->count() < $this->minimumPlayers) {
            return false;
        }

        return GameStepEnum::NEW === $game->getStep();
    }

    public function canReOpenGameInvitation(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getHost()->getLinkedUser()) {
            return false;
        }

        return GameStepEnum::CONFIGURATION === $game->getStep();
    }

    public function canSetConfiguration(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getHost()->getLinkedUser()) {
            return false;
        }

        return GameStepEnum::CONFIGURATION === $game->getStep();
    }

    public function canSetGameMaster(AbstractUser $user, Game $game): bool
    {
        if ($user !== $game->getHost()->getLinkedUser()) {
            return false;
        }

        return GameStepEnum::GAME_MASTER_CHOICE === $game->getStep();
    }
}
