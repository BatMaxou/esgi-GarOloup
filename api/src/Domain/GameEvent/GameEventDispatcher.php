<?php

namespace App\Domain\GameEvent;

use App\Domain\GameEvent\Interface\GameEventApplicatorInterface;
use App\Domain\GameEvent\Interface\GameEventCollectorInterface;
use App\Domain\GameEvent\Interface\GameEventDispatcherInterface;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Game\Game;

class GameEventDispatcher implements GameEventDispatcherInterface
{
    /** @var GameEventApplicatorInterface<GameEvent>[] $applicators */
    private array $applicators;

    /** @param iterable<GameEventApplicatorInterface<GameEvent>> $applicators */
    public function __construct(
        private readonly GameEventCollectorInterface $collector,
        iterable $applicators
    ) {
        $this->applicators = iterator_to_array($applicators);
        usort(
            $this->applicators,
            fn (GameEventApplicatorInterface $a, GameEventApplicatorInterface $b) => $a::getPriority() <=> $b::getPriority()
        );
    }

    public function dispatch(GameEvent $gameEvent): Game
    {
        $game = null;
        foreach ($this->applicators as $applicator) {
            if ($applicator->supports($gameEvent)) {
                $game = $applicator->apply($gameEvent);
            }
        }

        if (!$game) {
            throw new \RuntimeException(\sprintf('No applicator found for event "%s"', $gameEvent::class));
        }

        $this->collector->collect($gameEvent->setGame($game));

        return $game;
    }
}
