<?php

namespace App\Api\Provider\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Game\Game;
use App\Entity\User\AbstractUser;
use App\Enum\Game\GameGlobalStepEnum;
use App\Repository\Game\GameRepository;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @implements ProviderInterface<Game>
 */
class GameRecapProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly GameRepository $gameRepository,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?Game
    {
        $id = $uriVariables['id'] ?? null;
        if (null === $id) {
            return null;
        }

        $game = $this->gameRepository->find($id);
        if (null === $game || GameGlobalStepEnum::FINISH !== $game->getGlobalStep()) {
            return null;
        }

        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof AbstractUser || !$this->isPlayer($game, $currentUser)) {
            return null;
        }

        return $game;
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
