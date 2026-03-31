<?php

namespace App\Domain\Spec;

use App\Entity\Game\Game;

class ConfigurationSpec
{
    public function __construct(
        private readonly int $minimumPlayers,
    ) {
    }

    public function isValid(Game $game): bool
    {
        $composition = $game->getConfiguration()->getComposition();
        if (null === $composition || $composition->isEmpty()) {
            return false;
        }

        $playerNumber = $game->getPlayers()->count();
        if ($playerNumber < $this->minimumPlayers) {
            return false;
        }

        $roleCount = 0;
        $includedRoles = [];
        foreach ($composition->getRoles() as $roleEntry) {
            $count = $roleEntry->getCount();
            if ($count <= 0) {
                return false;
            }

            $role = $roleEntry->getRole();
            $roleMinPlayers = $role->getMinPlayers();
            if (null !== $roleMinPlayers && $playerNumber < $role->getMinPlayers()) {
                return false;
            }

            $roleMaxPerGame = $role->getMaxPerGame();
            if (null !== $roleMaxPerGame && $roleMaxPerGame < $count) {
                return false;
            }

            $type = $role->getType();
            if (!$type || \in_array($type->value, $includedRoles)) {
                return false;
            }

            $roleCount += $count;
            $includedRoles[] = $type->value;
        }

        return $playerNumber === $roleCount;
    }
}
