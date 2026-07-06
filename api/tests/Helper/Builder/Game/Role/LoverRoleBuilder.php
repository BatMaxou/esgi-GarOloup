<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\GameRole;
use App\Entity\Game\Role\LoverRole;
use App\Fixtures\Factory\Game\Role\LoverRoleFactory;
use App\Tests\Helper\Builder\AbstractBuilder;

/** @extends AbstractBuilder<LoverRole> */
class LoverRoleBuilder extends AbstractBuilder
{
    public ?GameRole $originalRole = null;
    public ?string $partnerPlayerId = null;

    protected function doBuild(): object
    {
        if (null === $this->originalRole || null === $this->partnerPlayerId) {
            throw new \LogicException('A lover role needs an original role and a partner player id');
        }

        return LoverRoleFactory::createOne([
            'originalRole' => $this->originalRole,
            'partnerPlayerId' => $this->partnerPlayerId,
        ]);
    }

    public function wrapping(GameRole $originalRole): static
    {
        $this->originalRole = $originalRole;

        return $this;
    }

    public function withPartner(string $partnerPlayerId): static
    {
        $this->partnerPlayerId = $partnerPlayerId;

        return $this;
    }
}
