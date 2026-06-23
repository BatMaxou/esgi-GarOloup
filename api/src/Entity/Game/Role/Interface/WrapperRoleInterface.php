<?php

namespace App\Entity\Game\Role\Interface;

use App\Entity\Game\Role\GameRole;

interface WrapperRoleInterface
{
    public function getOriginalRole(): GameRole;

    public function setOriginalRole(GameRole $originalRole): static;
}
