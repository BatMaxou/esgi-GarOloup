<?php

namespace App\Domain\GameEvent;

use App\Domain\GameEvent\Applicator\Interface\GameEventApplicatorInterface;
use App\Entity\Event\Game\GameEvent;
use App\Entity\Game;

class GameEventDispatcher
{
    /** @var GameEventApplicatorInterface<GameEvent>[] $applicators */
    private array $applicators;

    /** @param iterable<GameEventApplicatorInterface<GameEvent>> $applicators */
    public function __construct(iterable $applicators)
    {
        $this->applicators = iterator_to_array($applicators);
        usort(
            $this->applicators,
            fn (GameEventApplicatorInterface $a, GameEventApplicatorInterface $b) => $a::getPriority() <=> $b::getPriority()
        );
    }

    public function dispatch(GameEvent $gameEvent): Game
    {
        $game = null;
        // retrive user here for username ? $user =
        foreach ($this->applicators as $applicator) {
            if ($applicator->supports($gameEvent)) {
                $game = $applicator->apply($gameEvent);
                // collect here with user && game set ?
            }
        }

        if (!$game) {
            throw new \RuntimeException(\sprintf('No applicator found for event "%s"', $gameEvent::class));
        }

        return $game;
    }
}
