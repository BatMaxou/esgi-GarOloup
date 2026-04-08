<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Api\Model\BasicActionOutput;
use App\Domain\GameEvent\Exception\GameException;
use App\Domain\GameEvent\GameEventDispatcher;
use App\Domain\GameEvent\HttpGameExceptionMapper;
use App\Entity\Event\Game\JoinGameEvent;
use App\Entity\Game\Game;
use App\Entity\User\AbstractUser;
use App\Enum\TopicEnum;
use App\Repository\Game\GameRepository;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use App\Service\Mercure\TopicPublisher;
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
        private readonly TopicProvider $topicProvider,
        private readonly TopicCollector $topicCollector,
        private readonly TopicPublisher $topicPublisher,
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
            ->setGame($game)
            ->setUser($currentUser);

        try {
            $game = $this->gameEventDispatcher->dispatch($gameEvent);

            $this->handleTopicUpdates($game);
        } catch (GameException $e) {
            throw HttpGameExceptionMapper::getHttpExceptionFor($e);
        } catch (\Throwable $e) {
            throw $e;
        }

        return new BasicActionOutput(true);
    }

    private function handleTopicUpdates(Game $game): void
    {
        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game));

        $this->topicPublisher->publish();
    }
}
