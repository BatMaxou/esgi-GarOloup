<?php

namespace App\Api\Provider\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/** @implements ProviderInterface<UserInterface> */
class MeProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?UserInterface
    {
        return $this->security->getUser();
    }
}
