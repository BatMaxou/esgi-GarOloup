<?php

namespace App\Tests\Helper\Builder\Player;

use App\Entity\Player;
use App\Fixtures\Factory\PlayerFactory;
use App\Tests\Helper\Builder\AbstractBuilder;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\User\TempUserBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;

/** @extends AbstractBuilder<Player> */
class PlayerBuilder extends AbstractBuilder
{
    public ?UserBuilder $user = null;
    public ?TempUserBuilder $tempUser = null;
    public ?bool $dead = null;
    public ?GameBuilder $game = null;

    protected function doBuild(): object
    {
        return PlayerFactory::createOne([
            ...($this->user ? ['user' => $this->user->getEntity(), 'tempUser' => null] : []),
            ...($this->tempUser ? ['tempUser' => $this->tempUser->getEntity(), 'user' => null] : []),
            ...($this->dead ? ['dead' => $this->dead] : []),
            ...($this->game ? ['game' => $this->game->getEntity()] : []),
        ]);
    }

    public function withUser(UserBuilder $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function withTempUser(TempUserBuilder $tempUser): static
    {
        $this->tempUser = $tempUser;

        return $this;
    }

    public function dead(): static
    {
        $this->dead = true;

        return $this;
    }

    public function withGame(GameBuilder $gameBuilder): static
    {
        $this->game = $gameBuilder;

        return $this;
    }
}
