<?php

namespace App\Domain\Spec;

use App\Repository\PlayerRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class GameSpec
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public function canCreate(UserInterface $user): bool
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
}
