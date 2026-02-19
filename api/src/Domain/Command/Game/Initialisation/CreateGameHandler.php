<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Api\Model\Game\CreateGameOutput;
use App\Domain\Spec\GameSpec;
use App\Entity\Game;
use App\Entity\Player;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Generator\RefreshTokenGeneratorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
class CreateGameHandler
{
    public const TWELVE_HOURS_VALIDITY = 43200;

    public function __construct(
        private readonly Security $security,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly RefreshTokenGeneratorInterface $refreshTokenGenerator,
        private readonly PlayerRepository $playerRepository,
        private readonly EntityManagerInterface $em,
        private readonly GameSpec $gameSpec,
    ) {
    }

    public function __invoke(CreateGameCommand $command): CreateGameOutput
    {
        $currentUser = $this->security->getUser();
        if (null === $currentUser) {
            throw new AccessDeniedHttpException('You are not logged in');
        }
        if (!$this->gameSpec->canCreate($currentUser)) {
            throw new ConflictHttpException('You are already playing a game');
        }

        $player = new Player($currentUser);
        $game = new Game($player);

        $this->em->persist($player);
        $this->em->persist($game);
        $this->em->flush();

        return new CreateGameOutput($game->getJoinCode());
    }
}
