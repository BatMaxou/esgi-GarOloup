<?php

namespace App\Api\Provider\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/** @implements ProviderInterface<Player> */
class CurrentPlayerProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Player
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof UserInterface) {
            return null;
        }

        return $this->playerRepository->findCurrentByUser($currentUser);
    }
}
