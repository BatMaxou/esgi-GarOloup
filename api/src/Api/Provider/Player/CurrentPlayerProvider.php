<?php

namespace App\Api\Provider\Player;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Domain\Spec\PlayerSpec;
use App\Entity\Player;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/** @implements ProviderInterface<Player> */
class CurrentPlayerProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerSpec $playerSpec,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Player
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof UserInterface) {
            return null;
        }

        return $this->playerSpec->getCurrentPlayer($currentUser);
    }
}
