<?php

namespace App\Tests\Helper\Builder\Game;

use App\Entity\Game;
use App\Enum\GameStepEnum;
use App\Fixtures\Factory\GameFactory;
use App\Tests\Helper\Builder\AbstractBuilder;
use App\Tests\Helper\Builder\Player\PlayerBuilder;

/** @extends AbstractBuilder<Game> */
class GameBuilder extends AbstractBuilder
{
    public ?PlayerBuilder $host = null;
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
            ...($this->finished ? ['finished' => $this->finished] : []),
            ...($this->createdAt ? ['createdAt' => $this->createdAt] : []),
        ]);
    }

    public function withHost(PlayerBuilder $host): static
    {
        $this->host = $host;

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

    public function finished(): static
    {
        $this->finished = true;

        return $this;
    }

    public function createdAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
