<?php

namespace App\Api\Provider\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Game\Game;
use App\Entity\Game\Recap;
use App\Entity\User\AbstractUser;
use App\Repository\Game\GameRepository;
use App\Repository\Game\RecapRepository;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProviderInterface<Recap>
 */
class GameRecapProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly RecapRepository $recapRepository,
        private readonly GameRepository $gameRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Recap
    {
        $gameId = $uriVariables['gameId'] ?? null;
        if (null === $gameId) {
            return null;
        }

        $recap = $this->recapRepository->findOneBy(['gameId' => $gameId]);
        if (null === $recap) {
            return null;
        }

        $game = $this->gameRepository->find($gameId);
        if (null === $game) {
            return null;
        }

        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof AbstractUser || !$this->isPlayer($game, $currentUser)) {
            return null;
        }

        return $recap;
    }

    private function isPlayer(Game $game, AbstractUser $user): bool
    {
        foreach ($game->getPlayers() as $player) {
            if ((string) $player->getLinkedUser()->getId() === (string) $user->getId()) {
                return true;
            }
        }

        return false;
    }
}
