<?php

namespace App\Entity\Game\Role\Interface;

interface WerewolfVoterInterface
{
    public function getTargetPlayerId(): ?string;

    public function setTargetPlayerId(?string $targetPlayerId): static;
}
