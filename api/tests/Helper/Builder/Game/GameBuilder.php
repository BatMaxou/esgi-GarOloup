<?php

namespace App\Tests\Helper\Builder\Game;

use App\Entity\Game\Game;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Factory\Game\GameFactory;
use App\Tests\Helper\Builder\AbstractBuilder;

/** @extends AbstractBuilder<Game> */
class GameBuilder extends AbstractBuilder
{
    public ?PlayerBuilder $host = null;
    public ?PlayerBuilder $gameMaster = null;
    /** @var PlayerBuilder[] */
    public array $players = [];
    public ?string $joinCode = null;
    public ?int $maxPlayers = null;
    public ?int $maxTimeForDiscussion = null;
    public ?bool $public = null;
    public ?GameInitialisationStepEnum $initialisationStep = null;
    public ?GameRuntimeStepEnum $runtimeStep = null;
    public ?bool $finished = null;
    public ?\DateTimeInterface $createdAt = null;

    protected function doBuild(): object
    {
        return GameFactory::createOne([
            ...($this->host ? ['host' => $this->host->getEntity()] : []),
            ...($this->gameMaster ? ['gameMaster' => $this->gameMaster->getEntity()] : []),
            ...($this->joinCode ? ['joinCode' => $this->joinCode] : []),
            ...($this->initialisationStep ? ['initialisationStep' => $this->initialisationStep] : []),
            ...($this->runtimeStep ? ['runtimeStep' => $this->runtimeStep] : []),
            ...($this->createdAt ? ['createdAt' => $this->createdAt] : []),
            ...($this->maxPlayers ? ['maxPlayers' => $this->maxPlayers] : []),
            ...($this->maxTimeForDiscussion ? ['maxTimeForDiscussion' => $this->maxTimeForDiscussion] : []),
            ...($this->public ? ['public' => $this->public] : []),
            'players' => \array_map(fn (PlayerBuilder $player) => $player->getEntity(), $this->players),
        ]);
    }

    public function withHost(PlayerBuilder $host): static
    {
        $this->host = $host;

        if ($this->gameMaster !== $host) {
            $this->players[] = $host;
        }

        return $this;
    }

    public function withGameMaster(PlayerBuilder $gameMaster): static
    {
        $this->gameMaster = $gameMaster;
        if (\in_array($gameMaster, $this->players)) {
            unset($this->players[\array_search($gameMaster, $this->players)]);
        }

        return $this;
    }

    public function withJoinCode(string $joinCode): static
    {
        $this->joinCode = $joinCode;

        return $this;
    }

    public function withMaxPlayers(int $maxPlayers): static
    {
        $this->maxPlayers = $maxPlayers;

        return $this;
    }

    public function withMaxTimeForDiscussion(int $maxTimeForDiscussion): static
    {
        $this->maxTimeForDiscussion = $maxTimeForDiscussion;

        return $this;
    }

    public function withInitialisationStep(GameInitialisationStepEnum $initialisationStep): static
    {
        $this->initialisationStep = $initialisationStep;

        return $this;
    }

    public function withRuntimeStep(GameRuntimeStepEnum $runtimeStep): static
    {
        if (GameInitialisationStepEnum::FINISH !== $this->initialisationStep) {
            $this->withInitialisationStep(GameInitialisationStepEnum::FINISH);
        }

        $this->runtimeStep = $runtimeStep;

        return $this;
    }

    public function withPlayer(PlayerBuilder $player): static
    {
        $this->players[] = $player;

        return $this;
    }

    /** @param PlayerBuilder[] $players */
    public function withPlayers(array $players): static
    {
        foreach ($players as $player) {
            $this->withPlayer($player);
        }

        return $this;
    }

    public function closed(): static
    {
        $this->withInitialisationStep(GameInitialisationStepEnum::CONFIGURATION);

        return $this;
    }

    public function finished(): static
    {
        $this->withInitialisationStep(GameInitialisationStepEnum::FINISH);
        $this->withRuntimeStep(GameRuntimeStepEnum::FINISH);

        return $this;
    }

    public function createdAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function public(): static
    {
        $this->public = true;

        return $this;
    }
}
