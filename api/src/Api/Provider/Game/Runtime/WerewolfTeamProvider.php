<?php

namespace App\Api\Provider\Game\Runtime;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Api\Model\Game\WerewolfTeam;
use App\Domain\Spec\Role\WerewolfSpec;
use App\Entity\User\AbstractUser;
use App\Repository\Game\PlayerRepository;
use App\Service\Game\WerewolfTeamFactory;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/** @implements ProviderInterface<WerewolfTeam> */
class WerewolfTeamProvider implements ProviderInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
        private readonly WerewolfSpec $werewolfSpec,
        private readonly WerewolfTeamFactory $werewolfTeamFactory,
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

        if (!$this->werewolfSpec->canSeeTeam($player, $game)) {
            throw new AccessDeniedHttpException();
        }

        return $this->werewolfTeamFactory->fromGame($game);
    }
}
