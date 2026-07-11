<?php

namespace App\Domain\Spec\Role;

use App\Entity\Game\Game;
use App\Entity\Game\Player;
use App\Entity\Game\Role\CupidonRole;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class CupidonSpec
{
    public function __construct(
        private readonly ClockInterface $clock,
    ) {
    }

    public function canChooseLovers(Player $cupidon, Game $game, string $firstLoverId, string $secondLoverId): bool
    {
        if (
            GameRuntimeStepEnum::SETUP !== $game->getRuntimeStep()
            || $game->getStepEndAt() < $this->clock->now()
            || $firstLoverId === $secondLoverId
        ) {
            return false;
        }

        $found = 0;
        foreach ($game->getPlayers() as $candidate) {
            $candidateId = $candidate->getId()?->toString();
            if ($candidateId === $firstLoverId || $candidateId === $secondLoverId) {
                ++$found;
            }
        }

        $role = $cupidon->getRoleAs(CupidonRole::class);

        return 2 === $found && $role instanceof CupidonRole && !$role->isSetup();
    }
}
