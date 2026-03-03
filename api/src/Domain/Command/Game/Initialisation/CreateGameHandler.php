<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Api\Model\Game\CreateGameOutput;
use App\Domain\Spec\GameSpec;
use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User\AbstractUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateGameHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly GameSpec $gameSpec,
    ) {
    }

    public function __invoke(CreateGameCommand $command): CreateGameOutput
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof AbstractUser) {
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
