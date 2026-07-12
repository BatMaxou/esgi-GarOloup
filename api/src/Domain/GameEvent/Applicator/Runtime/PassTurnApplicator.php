<?php

namespace App\Domain\GameEvent\Applicator\Runtime;

use App\Domain\GameEvent\Applicator\Trait\GameAwareTrait;
use App\Domain\GameEvent\Applicator\Trait\UserAwareTrait;
use App\Domain\GameEvent\Exception\PlayerNotFoundException;
use App\Domain\GameEvent\Exception\UnauthorizedGameActionException;
use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\Spec\PassTurnSpec;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Event\Game\PassTurnEvent;
use App\Entity\Game\Game;
use App\Enum\TopicEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Mercure\TopicCollector;
use App\Service\Mercure\TopicProvider;
use Psr\Clock\ClockInterface;

/** @implements GameEventApplicatorInterface<PassTurnEvent> */
class PassTurnApplicator implements GameEventApplicatorInterface
{
    use GameAwareTrait;
    use UserAwareTrait;

    public function __construct(
        private readonly PassTurnSpec $passTurnSpec,
        private readonly PlayerRepository $playerRepository,
        private readonly ClockInterface $clock,
        private readonly TopicProvider $topicProvider,
        private readonly TopicCollector $topicCollector,
    ) {
    }

    public static function getPriority(): int
    {
        return static::DEFAULT_PRIORITY;
    }

    public function apply(GameEvent $gameEvent): Game
    {
        $user = $this->ensureUser($gameEvent);
        $game = $this->ensureGame($gameEvent);

        $player = $this->playerRepository->findCurrentByUser($user);
        if (null === $player) {
            throw new PlayerNotFoundException('Current player not found');
        }

        if (!$this->passTurnSpec->canPass($player, $game)) {
            throw new UnauthorizedGameActionException('You can not pass your turn');
        }

        $game->setStepEndAt($this->clock->now());
        $this->topicCollector->collect($this->topicProvider->provide(TopicEnum::CURRENT_GAME, $game));

        return $game;
    }

    public function supports(GameEvent $gameEvent): bool
    {
        return $gameEvent instanceof PassTurnEvent;
    }
}
