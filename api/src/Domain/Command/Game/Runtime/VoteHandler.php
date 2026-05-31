<?php

namespace App\Domain\Command\Game\Runtime;

use App\Api\Model\BasicActionOutput;
use App\Domain\GameEvent\Exception\GameException;
use App\Domain\GameEvent\GameEventDispatcher;
use App\Domain\GameEvent\HttpGameExceptionMapper;
use App\Entity\Event\Game\VoteEvent;
use App\Entity\Game\Game;
use App\Entity\User\AbstractUser;
use App\Enum\TopicEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use App\Service\Mercure\TopicPublisher;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class VoteHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
        private readonly GameEventDispatcher $gameEventDispatcher,
        private readonly TopicCollector $topicCollector,
        private readonly TopicProvider $topicProvider,
        private readonly TopicPublisher $topicPublisher,
    ) {
    }

    public function __invoke(VoteCommand $command): BasicActionOutput
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof AbstractUser) {
            throw new AccessDeniedHttpException('You are not logged in');
        }

        $player = $this->playerRepository->findCurrentByUser($currentUser);
        if (null === $player) {
            throw new AccessDeniedHttpException('You do not have a current player');
        }

        $game = $player->getLinkedGame();
        if (null === $game) {
            throw new AccessDeniedHttpException('You do not have a current game');
        }

        $gameEvent = new VoteEvent()
            ->setGame($game)
            ->setUser($currentUser)
            ->setTargetPlayerId($command->targetPlayerId);

        try {
            $game = $this->gameEventDispatcher->dispatch($gameEvent);
            $this->handleTopicUpdates($game);
        } catch (GameException $e) {
            throw HttpGameExceptionMapper::getHttpExceptionFor($e);
        }

        return new BasicActionOutput(true);
    }

    private function handleTopicUpdates(Game $game): void
    {
        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game));

        $this->topicPublisher->publish();
    }
}
