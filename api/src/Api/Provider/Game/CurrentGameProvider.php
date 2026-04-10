<?php

namespace App\Api\Provider\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Game\Game;
use App\Entity\User\AbstractUser;
use App\Repository\Game\PlayerRepository;
use Symfony\Bundle\SecurityBundle\Security;

/** @implements ProviderInterface<Game> */
class CurrentGameProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Game
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof AbstractUser) {
            return null;
        }

        $player = $this->playerRepository->findCurrentByUser($currentUser);
        if (!$player) {
            return null;
        }

        return $player->getGame() ?? $player->getManagedGame();
    }
}
