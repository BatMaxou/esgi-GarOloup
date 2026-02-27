<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Domain\Spec\GameSpec;
use App\Entity\Game;
use App\Entity\Player;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class JoinGameHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly GameRepository $gameRepository,
        private readonly EntityManagerInterface $em,
        private readonly GameSpec $gameSpec,
    ) {
    }

    public function __invoke(JoinGameCommand $command): void
    {
        $currentUser = $this->security->getUser();
        if (null === $currentUser) {
            throw new AccessDeniedHttpException('You are not logged in');
        }

        if (!$this->gameSpec->canJoin($currentUser)) {
            throw new ConflictHttpException('You are already playing a game');
        }

        $game = $this->gameRepository->findByJoinCode($command->joinCode);
        if (!$game instanceof Game) {
            throw new NotFoundHttpException('Game not found');
        }

        $player = new Player($currentUser);
        $game->addPlayer($player);

        $this->em->persist($player);
        $this->em->flush();
    }
}
