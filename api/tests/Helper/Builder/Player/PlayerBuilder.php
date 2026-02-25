<?php

namespace App\Tests\Helper\Builder\Player;

use App\Entity\Player;
use App\Fixtures\Factory\PlayerFactory;
use App\Tests\Helper\Builder\AbstractBuilder;
use App\Tests\Helper\Builder\User\TempUserBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;

/** @extends AbstractBuilder<Player> */
class PlayerBuilder extends AbstractBuilder
{
    public ?UserBuilder $user = null;
    public ?TempUserBuilder $tempUser = null;
    public ?bool $isDead = null;

    protected function doBuild(): object
    {
        return PlayerFactory::createOne([
            ...($this->user ? ['user' => $this->user->getEntity()] : []),
            ...($this->tempUser ? ['tempUser' => $this->tempUser->getEntity()] : []),
            ...($this->isDead ? ['isDead' => $this->isDead] : []),
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

    public function isDead(): static
    {
        $this->isDead = true;

        return $this;
    }
}
