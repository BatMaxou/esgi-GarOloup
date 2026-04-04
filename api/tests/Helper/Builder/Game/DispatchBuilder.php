<?php

namespace App\Tests\Helper\Builder\Game;

use App\Api\Model\Game\Dispatch\RoleDispatchEntryInput;
use App\Tests\Helper\Builder\Role\RoleBuilder;

class DispatchBuilder
{
    /** @var array<array{player: PlayerBuilder, role: RoleBuilder}> */
    public array $entries = [];

    public function withEntry(PlayerBuilder $player, RoleBuilder $role): static
    {
        $this->entries[] = ['player' => $player, 'role' => $role];

        return $this;
    }

    /**
     * @return RoleDispatchEntryInput[]
     */
    public function toInput(): array
    {
        $inputs = [];
        foreach ($this->entries as $entry) {
            $playerId = $entry['player']->getEntity()->getId();
            $type = $entry['role']->getEntity()->getType();
            if (!$playerId || !$type) {
                continue;
            }

            $inputs[] = new RoleDispatchEntryInput($playerId, $type);
        }

        return $inputs;
    }
}
