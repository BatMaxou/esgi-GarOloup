<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Api\Model\BasicActionOutput;
use App\Domain\GameEvent\Exception\GameException;
use App\Domain\GameEvent\GameEventDispatcher;
use App\Domain\GameEvent\HttpGameExceptionMapper;
use App\Entity\Event\Game\SetGameConfigurationEvent;
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
class SetGameConfigurationHandler
{
    public function __construct(
        private readonly Security $security,
        private readonly PlayerRepository $playerRepository,
        private readonly GameEventDispatcher $gameEventDispatcher,
        private readonly TopicProvider $topicProvider,
        private readonly TopicCollector $topicCollector,
        private readonly TopicPublisher $topicPublisher,
    ) {
    }

    public function __invoke(SetGameConfigurationCommand $command): BasicActionOutput
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof AbstractUser) {
            throw new AccessDeniedHttpException('You are not logged in');
        }

        $player = $this->playerRepository->findCurrentByUser($currentUser);
        if (null === $player) {
            throw new AccessDeniedHttpException('You do not have a current player');
        }

        $gameEvent = new SetGameConfigurationEvent()
            ->setGame($player->getLinkedGame())
            ->setUser($currentUser)
            ->setComposition($command->composition)
            ->setWithGameMaster($command->withGameMaster)
            ->setWithRandomDispatch($command->withRandomDispatch);

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
