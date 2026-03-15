<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Api\Model\BasicActionOutput;
use App\Domain\GameEvent\Applicator\Exception\GameException;
use App\Domain\GameEvent\GameEventDispatcher;
use App\Domain\GameEvent\HttpGameExceptionMapper;
use App\Entity\Event\Game\JoinGameEvent;
use App\Entity\Game;
use App\Entity\User\AbstractUser;
use App\Repository\GameRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class JoinGameHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly GameEventDispatcher $gameEventDispatcher,
        private readonly GameRepository $gameRepository,
    ) {
    }

    public function __invoke(JoinGameCommand $command): BasicActionOutput
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof AbstractUser) {
            throw new AccessDeniedHttpException('You are not logged in');
        }

        $game = $this->gameRepository->findByJoinCode($command->joinCode);
        if (!$game instanceof Game) {
            throw new NotFoundHttpException('Game not found');
        }

        $gameEvent = new JoinGameEvent()
            ->setUser($currentUser)
            ->setGame($game);

        try {
            $this->gameEventDispatcher->dispatch($gameEvent);
        } catch (GameException $e) {
            throw HttpGameExceptionMapper::getHttpExceptionFor($e);
        } catch (\Throwable $e) {
            throw $e;
        }

        return new BasicActionOutput(true);
    }
}
