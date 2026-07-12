<?php

namespace App\Entity\Game\Period\Action\Trait;

use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

trait RevealedRoleActionTrait
{
    #[ORM\Column(enumType: GameRoleEnum::class, nullable: true)]
    private ?GameRoleEnum $revealedRole = null;

    public function getRevealedRole(): ?GameRoleEnum
    {
        return $this->revealedRole;
    }

    public function setRevealedRole(?GameRoleEnum $revealedRole): static
    {
        $this->revealedRole = $revealedRole;

        return $this;
    }
}
