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
        $configuration = $game->getConfiguration();
        $composition = $configuration->getComposition();
        if (
            null === $composition
            || $composition->isEmpty()
            || (
                !$configuration->isWithRandomDispatch()
                && !$configuration->isWithGameMaster()
            )
        ) {
            return false;
        }

        $playerNumber = $game->getPlayers()->count() - ($configuration->isWithGameMaster() ? 1 : 0);
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
