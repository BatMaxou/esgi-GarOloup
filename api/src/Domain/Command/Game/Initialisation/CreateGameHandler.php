<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Api\Model\Game\CreateGameOutput;
use App\Domain\GameEvent\Applicator\Exception\GameException;
use App\Domain\GameEvent\GameEventDispatcher;
use App\Domain\GameEvent\HttpGameExceptionMapper;
use App\Entity\Event\Game\CreateGameEvent;
use App\Entity\User\AbstractUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateGameHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly GameEventDispatcher $gameEventDispatcher,
    ) {
    }

    public function __invoke(CreateGameCommand $command): CreateGameOutput
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof AbstractUser) {
            throw new AccessDeniedHttpException('You are not logged in');
        }

        $gameEvent = new CreateGameEvent()->setUser($currentUser);

        try {
            $game = $this->gameEventDispatcher->dispatch($gameEvent);
        } catch (GameException $e) {
            throw HttpGameExceptionMapper::getHttpExceptionFor($e);
        } catch (\Throwable $e) {
            throw $e;
        }

        return new CreateGameOutput($game->getJoinCode());
    }
}
