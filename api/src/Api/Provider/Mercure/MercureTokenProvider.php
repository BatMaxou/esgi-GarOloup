<?php

namespace App\Api\Provider\Mercure;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Mercure\MercureToken;
use App\Repository\Game\PlayerRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/** @implements ProviderInterface<MercureToken> */
class MercureTokenProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
        private readonly HubInterface $hub,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): MercureToken
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof UserInterface) {
            return new MercureToken();
        }

        $factory = $this->hub->getFactory();
        if (null === $factory) {
            return new MercureToken();
        }

        // put to service
        $player = $this->playerRepository->findCurrentByUser($currentUser);
        $game = $player?->getGame();

        $token = $factory->create(subscribe: [
            \sprintf('http://localhost:8000/api/games/%s', $game->getId() ?? ''),
            \sprintf('http://localhost:8000/api/players/%s', $player->getId() ?? ''),
        ], publish: []);

        return new MercureToken($token);
    }
}
