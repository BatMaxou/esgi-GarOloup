<?php

namespace App\Entity\Game\Role;

use ApiPlatform\Metadata\ApiResource;
use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Entity\Game\Role\Interface\WrapperRoleInterface;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(operations: [])]
class LoverRole extends GameRole implements WrapperRoleInterface, NightResettableInterface
{
    #[ORM\ManyToOne(targetEntity: GameRole::class, cascade: ['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private GameRole $originalRole;

    #[ORM\Column(length: 36)]
    private string $partnerPlayerId;

    public function __construct(GameRole $originalRole, string $partnerPlayerId)
    {
        parent::__construct();
        $this->setOriginalRole($originalRole);
        $this->partnerPlayerId = $partnerPlayerId;
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

    public function getPartnerPlayerId(): string
    {
        return $this->partnerPlayerId;
    }

    public function getType(): ?GameRoleEnum
    {
        return $this->originalRole->getType() ?? ($this->type ?? null);
    }

    public function isInfected(): bool
    {
        return $this->originalRole->isInfected();
    }

    public function isSetup(): bool
    {
        return $this->originalRole->isSetup();
    }

    public function setSetup(bool $isSetup): static
    {
        $this->originalRole->setSetup($isSetup);

        return $this;
    }

    public function clearNightState(): void
    {
        if ($this->originalRole instanceof NightResettableInterface) {
            $this->originalRole->clearNightState();
        }
    }

    protected function needSetup(): bool
    {
        return false;
    }

    /**
     * @param array<int, mixed> $arguments
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->originalRole->{$name}(...$arguments);
    }
}
