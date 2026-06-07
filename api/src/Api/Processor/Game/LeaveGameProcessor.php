<?php

namespace App\Api\Processor\Game;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Domain\GameEvent\Exception\GameException;
use App\Domain\GameEvent\GameEventDispatcher;
use App\Domain\GameEvent\HttpGameExceptionMapper;
use App\Entity\Event\Game\LeaveGameEvent;
use App\Entity\Game\Game;
use App\Entity\User\AbstractUser;
use App\Enum\TopicEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use App\Service\Mercure\TopicPublisher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/** @implements ProcessorInterface<mixed, void> */
final class LeaveGameProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
        private readonly GameEventDispatcher $gameEventDispatcher,
        private readonly EntityManagerInterface $em,
        private readonly TopicCollector $topicCollector,
        private readonly TopicProvider $topicProvider,
        private readonly TopicPublisher $topicPublisher,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof AbstractUser) {
            throw new AccessDeniedHttpException('You are not logged in');
        }

        $player = $this->playerRepository->findCurrentByUser($currentUser);
        if (null === $player) {
            throw new AccessDeniedHttpException('You do not have a current player');
        }

        $gameEvent = new LeaveGameEvent()
            ->setGame($player->getLinkedGame())
            ->setUser($currentUser);

        try {
            $game = $this->gameEventDispatcher->dispatch($gameEvent, true);

            if ($game->getPlayers()->isEmpty()) {
                $this->em->remove($game);
                $this->em->flush();
            }

            $this->handleTopicUpdates($game);
        } catch (GameException $e) {
            throw HttpGameExceptionMapper::getHttpExceptionFor($e);
        }
    }

    private function handleTopicUpdates(Game $game): void
    {
        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game));

        $this->topicPublisher->publish();
    }
}
