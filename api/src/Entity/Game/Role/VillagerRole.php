<?php

namespace App\Entity\Game\Role;

use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class VillagerRole extends GameRole
{
    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::VILLAGER;
    }

    protected function needSetup(): bool
    {
        return false;
    }
}
