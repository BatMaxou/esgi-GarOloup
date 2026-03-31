<?php

namespace App\Tests\Helper\Builder\Game;

use App\Entity\Game\Game;
use App\Enum\Game\GameStepEnum;
use App\Fixtures\Factory\Game\GameFactory;
use App\Tests\Helper\Builder\AbstractBuilder;

/** @extends AbstractBuilder<Game> */
class GameBuilder extends AbstractBuilder
{
    public ?PlayerBuilder $host = null;
    /** @var PlayerBuilder[] */
    public array $players = [];
    public ?string $joinCode = null;
    public ?GameStepEnum $step = null;
    public ?bool $finished = null;
    public ?\DateTimeInterface $createdAt = null;

    protected function doBuild(): object
    {
        return GameFactory::createOne([
            ...($this->host ? ['host' => $this->host->getEntity()] : []),
            ...($this->joinCode ? ['joinCode' => $this->joinCode] : []),
            ...($this->step ? ['step' => $this->step] : []),
            ...($this->createdAt ? ['createdAt' => $this->createdAt] : []),
            'players' => array_map(fn (PlayerBuilder $player) => $player->getEntity(), $this->players),
        ]);
    }

    public function withHost(PlayerBuilder $host): static
    {
        $this->host = $host;
        $this->players[] = $host;

        return $this;
    }

    public function withJoinCode(string $joinCode): static
    {
        $this->joinCode = $joinCode;

        return $this;
    }

    public function withStep(GameStepEnum $step): static
    {
        $this->step = $step;

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
        $this->withStep(GameStepEnum::CONFIGURATION);

        return $this;
    }

    public function finished(): static
    {
        $this->withStep(GameStepEnum::FINISH);

        return $this;
    }

    public function createdAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
