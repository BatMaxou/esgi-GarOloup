<?php

namespace App\Domain\Command\Game\Runtime;

use App\Api\Model\BasicActionOutput;
use App\Domain\GameEvent\Exception\GameException;
use App\Domain\GameEvent\GameEventDispatcher;
use App\Domain\GameEvent\HttpGameExceptionMapper;
use App\Entity\Event\Game\TimeUpGameEvent;
use App\Entity\Game\Game;
use App\Entity\User\AbstractUser;
use App\Enum\TopicEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use App\Service\Mercure\TopicPublisher;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class TimeUpHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
        private readonly GameEventDispatcher $gameEventDispatcher,
        private readonly TopicCollector $topicCollector,
        private readonly TopicProvider $topicProvider,
        private readonly TopicPublisher $topicPublisher,
        private readonly EntityManagerInterface $em,
        private readonly ClockInterface $clock,
    ) {
    }

    public function __invoke(TimeUpCommand $command): BasicActionOutput
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

        $timerNotExpired = $this->em->wrapInTransaction(function () use ($game, $currentUser): bool {
            $this->em->lock($game, LockMode::PESSIMISTIC_WRITE);
            $this->em->refresh($game);

            $stepEndAt = $game->getStepEndAt();
            if (null === $stepEndAt) {
                return false;
            }

            if ($stepEndAt > $this->clock->now()) {
                return true;
            }

            $gameEvent = new TimeUpGameEvent()
                ->setGame($game)
                ->setUser($currentUser);

            try {
                $game = $this->gameEventDispatcher->dispatch($gameEvent, true);
                $this->handleTopicUpdates($game);
            } catch (GameException $e) {
                throw HttpGameExceptionMapper::getHttpExceptionFor($e);
            }

            return false;
        });

        if ($timerNotExpired) {
            throw new ConflictHttpException('The step timer has not expired yet');
        }

        return new BasicActionOutput(true);
    }

    private function handleTopicUpdates(Game $game): void
    {
        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game));
        $this->topicPublisher->publish();
    }
}
