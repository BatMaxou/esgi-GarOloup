<?php

namespace App\Api\Provider\Game\Runtime;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Game\WerewolfTeam;
use App\Domain\Spec\GameSpec;
use App\Entity\User\AbstractUser;
use App\Repository\Game\PlayerRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/** @implements ProviderInterface<WerewolfTeam> */
class WerewolfTeamProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
        private readonly GameSpec $gameSpec,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ?WerewolfTeam
    {
        $user = $this->security->getUser();
        if (!$user instanceof AbstractUser) {
            return null;
        }

        $player = $this->playerRepository->findCurrentByUser($user);
        if (!$player) {
            return null;
        }

        $game = $player->getGame();
        if (!$game) {
            return null;
        }

        if (!$this->gameSpec->canSeeWerewolfTeam($player, $game)) {
            throw new AccessDeniedHttpException();
        }

        $gameId = $game->getId();
        if (!$gameId) {
            return null;
        }

        $members = $this->playerRepository->findWerewolvesByGame($game);

        return new WerewolfTeam((string) $gameId, $members);
    }
}
