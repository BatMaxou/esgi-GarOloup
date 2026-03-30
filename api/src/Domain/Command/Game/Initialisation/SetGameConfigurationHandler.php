<?php

namespace App\Domain\Command\Game\Initialisation;

use App\Api\Model\BasicActionOutput;
use App\Domain\GameEvent\Exception\GameException;
use App\Domain\GameEvent\GameEventDispatcher;
use App\Domain\GameEvent\HttpGameExceptionMapper;
use App\Entity\Event\Game\SetConfigurationEvent;
use App\Entity\User\AbstractUser;
use App\Repository\Game\PlayerRepository;
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

        $gameEvent = new SetConfigurationEvent()
            ->setComposition($command->composition)
            ->setWithGameMaster($command->withGameMaster)
            ->setWithRandomDispatch($command->withRandomDispatch)
            ->setUser($currentUser)
            ->setGame($player->getGame());

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
