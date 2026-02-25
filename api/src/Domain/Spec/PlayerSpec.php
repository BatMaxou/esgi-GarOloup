<?php

namespace App\Domain\Spec;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class PlayerSpec
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public function getCurrentPlayer(UserInterface $user): ?Player
    {
        return $this->playerRepository->findCurrentByUser($user);
    }
}
