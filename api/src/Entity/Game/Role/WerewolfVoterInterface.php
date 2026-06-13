<?php

namespace App\Entity\Game\Role;

interface WerewolfVoterInterface
{
    public function getTargetPlayerId(): ?string;

    public function setTargetPlayerId(?string $targetPlayerId): static;
}
