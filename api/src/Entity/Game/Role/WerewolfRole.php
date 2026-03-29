<?php

namespace App\Entity\Game\Role;

use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WerewolfRole extends GameRole
{
    public function __construct()
    {
        $this->type = GameRoleEnum::WEREWOLF;
    }
}
