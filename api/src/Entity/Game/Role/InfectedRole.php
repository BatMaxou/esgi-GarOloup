<?php

namespace App\Entity\Game\Role;

use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Entity\Game\Role\Interface\WrapperRoleInterface;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class InfectedRole extends WerewolfRole implements WrapperRoleInterface
{
    #[ORM\ManyToOne(targetEntity: GameRole::class)]
    #[ORM\JoinColumn(nullable: false)]
    private GameRole $originalRole;

    public function __construct(GameRole $originalRole)
    {
        parent::__construct();
        $this->setOriginalRole($originalRole);
    }

    public function getOriginalRole(): GameRole
    {
        return $this->originalRole;
    }

    public function setOriginalRole(GameRole $originalRole): static
    {
        $this->originalRole = $originalRole;
        $type = $originalRole->getType();
        if (null !== $type) {
            $this->type = $type;
        }

        return $this;
    }

    public function getType(): ?GameRoleEnum
    {
        return $this->originalRole->getType() ?? ($this->type ?? null);
    }

    public function clearNightState(): void
    {
        parent::clearNightState();

        if ($this->originalRole instanceof NightResettableInterface) {
            $this->originalRole->clearNightState();
        }
    }

    /**
     * @param array<int, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->originalRole->{$name}(...$arguments);
    }
}
