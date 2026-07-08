<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Assassin;

use App\Entity\Game\Game;
use App\Entity\Game\Role\AssassinRole;
use App\Entity\Game\Role\Interface\WerewolfVoterInterface;
use App\Enum\Game\GameRoleEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

trait AssassinEndgameTrait
{
    protected function assassinNightKill(GameBuilder $gameBuilder, PlayerBuilder $victim): void
    {
        $assassinPlayerBuilder = $this->getState(self::ASSASSIN);
        \assert($assassinPlayerBuilder instanceof PlayerBuilder);
        $assassinRole = $assassinPlayerBuilder->getEntity()->getRoleAs(AssassinRole::class);
        \assert($assassinRole instanceof AssassinRole);
        $assassinRole->markActedThisNight();

        ThereIs::aMurderAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::ASSASSIN)
            ->against($victim)
            ->build();
    }

    protected function werewolfNightKill(GameBuilder $gameBuilder, PlayerBuilder $victim): void
    {
        $victimId = $victim->getEntity()->getId()?->toString()
            ?? throw new \LogicException('Target player id should not be null');

        foreach ($this->getPool(self::WEREWOLVES_POOL) as $werewolfPlayerBuilder) {
            \assert($werewolfPlayerBuilder instanceof PlayerBuilder);
            $werewolfPlayer = $werewolfPlayerBuilder->getEntity();
            if ($werewolfPlayer->isDead()) {
                continue;
            }

            $werewolfRole = $werewolfPlayer->getRole();
            \assert($werewolfRole instanceof WerewolfVoterInterface);
            $werewolfRole->setTargetPlayerId($victimId);
        }

        ThereIs::aMurderAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::WEREWOLF)
            ->against($victim)
            ->build();
    }

    protected function resolveNight(Game $game): void
    {
        $workflow = $game->getNightWorkflow();
        while (null !== $workflow && !$workflow->isCompleted()) {
            $this->nightOrchestrator->advance($game);
        }
    }

    protected function villageVoteOut(GameBuilder $gameBuilder, PlayerBuilder $target): void
    {
        $targetId = $target->getEntity()->getId()?->toString();

        foreach ($this->getPool(self::PLAYERS_POOL) as $voterBuilder) {
            \assert($voterBuilder instanceof PlayerBuilder);
            $voter = $voterBuilder->getEntity();
            if ($voter->isDead() || $voter->getId()?->toString() === $targetId) {
                continue;
            }

            ThereIs::aBallot()
                ->forGame($gameBuilder)
                ->by($voterBuilder)
                ->against($target)
                ->build();
        }
    }
}
