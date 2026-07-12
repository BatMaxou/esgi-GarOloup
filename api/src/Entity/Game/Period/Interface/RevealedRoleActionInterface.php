<?php

namespace App\Entity\Game\Period\Interface;

use App\Enum\Game\GameRoleEnum;

interface RevealedRoleActionInterface
{
    public function getRevealedRole(): ?GameRoleEnum;

    public function setRevealedRole(?GameRoleEnum $revealedRole): static;
}
