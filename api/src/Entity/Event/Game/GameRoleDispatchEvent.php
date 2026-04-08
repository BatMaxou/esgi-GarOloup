<?php

namespace App\Entity\Event\Game;

use App\Api\Model\Game\Dispatch\RoleDispatchEntryInput;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class GameRoleDispatchEvent extends GameEvent
{
    /** @var RoleDispatchEntryInput[] */
    private array $dispatch = [];

    /** @return RoleDispatchEntryInput[] */
    public function getDispatch(): array
    {
        return $this->dispatch;
    }

    /** @param RoleDispatchEntryInput[] $dispatch */
    public function setDispatch(array $dispatch): self
    {
        $this->dispatch = $dispatch;

        return $this;
    }
}
