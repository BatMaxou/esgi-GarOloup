<?php

namespace App\Domain\Workflow\TurnRule;

use App\Domain\Workflow\TurnRule\Interface\TurnRuleInterface;
use App\Entity\Game\Game;
use App\Enum\Game\GameRoleEnum;

class TurnValidator
{
    /** @var TurnRuleInterface[] */
    private array $rules;

    /** @param iterable<TurnRuleInterface> $rules */
    public function __construct(iterable $rules)
    {
        $this->rules = \iterator_to_array($rules);
    }

    public function shouldPlay(Game $game, GameRoleEnum $role): bool
    {
        foreach ($this->rules as $rule) {
            if ($rule->supports($role)) {
                return $rule->shouldPlay($game, $role);
            }
        }

        $player = $game->getPlayer($role);

        return null !== $player && !$player->isDead();
    }
}
