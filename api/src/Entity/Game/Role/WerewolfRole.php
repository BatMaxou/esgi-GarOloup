<?php

namespace App\Entity\Game\Role;

use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class WerewolfRole extends GameRole implements NightResettableInterface
{
    #[ORM\Column(length: 36, nullable: true)]
    private ?string $targetPlayerId = null;

    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::WEREWOLF;
    }

    protected function needSetup(): bool
    {
        return false;
    }

    public function getTargetPlayerId(): ?string
    {
        return $this->targetPlayerId;
    }

    public function setTargetPlayerId(?string $targetPlayerId): static
    {
        $this->targetPlayerId = $targetPlayerId;

        return $this;
    }

    public function clearNightState(): void
    {
        $this->targetPlayerId = null;
    }
}
