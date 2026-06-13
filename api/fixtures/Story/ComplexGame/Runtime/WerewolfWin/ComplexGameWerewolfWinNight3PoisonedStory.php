<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin;

use App\Entity\Game\Role\WerewolfVoterInterface;
use App\Entity\Game\Role\WildChildRole;
use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class ComplexGameWerewolfWinNight3PoisonedStory extends ComplexGameWerewolfWinNight3Story
{
    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $witchPlayerBuilder = $this->getState(self::WITCH);
        \assert($witchPlayerBuilder instanceof PlayerBuilder);
        $witchRole = $witchPlayerBuilder->getEntity()->getRole();
        \assert($witchRole instanceof WitchRole);

        $lastWerewolfPlayerBuilder = $this->getState(self::WEREWOLF_3);
        \assert($lastWerewolfPlayerBuilder instanceof PlayerBuilder);

        ThereIs::aMurderAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::WITCH)
            ->against($lastWerewolfPlayerBuilder)
            ->build();

        $witchRole->usePoisonPotion();

        $this->nightOrchestrator->advance($game);
        $this->dayOrchestrator->start($game);

        $wildChildPlayerBuilder = $this->getState(self::WILD_CHILD);
        \assert($wildChildPlayerBuilder instanceof PlayerBuilder);
        $wildChildPlayer = $wildChildPlayerBuilder->getEntity();
        $wildChildRole = $wildChildPlayer->getRole();
        \assert($wildChildRole instanceof WildChildRole);
        $wildChildRole->transform();
        $wildChildPlayer->setTeam(GameTeamEnum::WEREWOLF);

        $this->em->flush();
    }

    protected function castVillageMisvote(GameBuilder $gameBuilder, PlayerBuilder $target): void
    {
        $targetId = $target->getEntity()->getId()?->toString();

        foreach ($this->getAllPlayerStates() as $stateName) {
            $voterBuilder = $this->getState($stateName);
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

    protected function castWerewolfKill(GameBuilder $gameBuilder, PlayerBuilder $victim): void
    {
        $wildChildPlayerBuilder = $this->getState(self::WILD_CHILD);
        \assert($wildChildPlayerBuilder instanceof PlayerBuilder);
        $wildChildRole = $wildChildPlayerBuilder->getEntity()->getRole();
        \assert($wildChildRole instanceof WerewolfVoterInterface);

        $victimId = $victim->getEntity()->getId()?->toString();
        \assert(null !== $victimId);
        $wildChildRole->setTargetPlayerId($victimId);

        ThereIs::aMurderAction()
            ->forGame($gameBuilder)
            ->from(GameRoleEnum::WEREWOLF)
            ->against($victim)
            ->build();
    }

    /**
     * @return string[]
     */
    protected function getAllPlayerStates(): array
    {
        return [
            self::VILLAGER_1, self::VILLAGER_2, self::VILLAGER_3, self::VILLAGER_4,
            self::VILLAGER_5, self::VILLAGER_6, self::SEER, self::WEREWOLF_1,
            self::WEREWOLF_2, self::WEREWOLF_3, self::WITCH, self::WILD_CHILD,
        ];
    }

    public function getPrefix(): string
    {
        return 'complex-game-werewolf-win-night-3-poisoned-';
    }
}
