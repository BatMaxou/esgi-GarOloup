<?php

namespace App\Entity\Game\Role;

use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class VillagerRole extends GameRole
{
    #[ORM\Column(nullable: true)]
    private ?string $friendId = null;

    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::VILLAGER;
    }

    public function getFriendId(): ?string
    {
        return $this->friendId;
    }

    public function setFriendId(?string $friendId): static
    {
        $this->friendId = $friendId;

        return $this;
    }

    protected function needSetup(): bool
    {
        return true;
    }
}
