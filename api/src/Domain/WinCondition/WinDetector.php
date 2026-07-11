<?php

namespace App\Domain\WinCondition;

use App\Domain\WinCondition\Interface\WinConditionCheckerInterface;
use App\Entity\Game\Game;

class WinDetector
{
    /** @var WinConditionCheckerInterface[] */
    private array $checkers;

    /** @param iterable<WinConditionCheckerInterface> $checkers */
    public function __construct(iterable $checkers)
    {
        $this->checkers = \iterator_to_array($checkers);
        \usort(
            $this->checkers,
            fn (WinConditionCheckerInterface $a, WinConditionCheckerInterface $b) => $a::getPriority() <=> $b::getPriority(),
        );
    }

    public function detect(Game $game): ?WinResult
    {
        foreach ($this->checkers as $checker) {
            if ($checker->supports($game) && $checker->isMet($game)) {
                return $checker->getWinResult();
            }
        }

        return null;
    }
}
