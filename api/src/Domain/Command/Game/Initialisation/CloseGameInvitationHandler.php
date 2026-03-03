<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Api\Model\BasicActionOutput;
use App\Domain\Spec\GameSpec;
use App\Entity\User\AbstractUser;
use App\Enum\GameStepEnum;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CloseGameInvitationHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly EntityManagerInterface $em,
        private readonly GameSpec $gameSpec,
        private readonly PlayerRepository $playerRepository,
    ) {
    }

    public function __invoke(CloseGameInvitationCommand $command): BasicActionOutput
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof AbstractUser) {
            throw new AccessDeniedHttpException('You are not logged in');
        }

        $player = $this->playerRepository->findCurrentByUser($currentUser);
        if (null === $player) {
            throw new AccessDeniedHttpException('You do not have a current player');
        }

        $game = $player->getGame();
        if (!$game) {
            throw new AccessDeniedHttpException('You are not in a game');
        }

        if (!$this->gameSpec->canCloseGameInvitation($currentUser, $game)) {
            throw new AccessDeniedHttpException('You can not close this game invitation');
        }

        $game->setStep(GameStepEnum::CONFIGURATION);

        $this->em->flush();

        return new BasicActionOutput(true);
    }
}
