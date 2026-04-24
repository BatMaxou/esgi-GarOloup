<?php

namespace App\Entity\Game\Role;

use App\Entity\Role;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameRoleEnum;
use App\Repository\Game\Role\GameRoleRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: GameRoleRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    GameRoleEnum::VILLAGER->value => VillagerRole::class,
    GameRoleEnum::WEREWOLF->value => WerewolfRole::class,
])]
abstract class GameRole
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne]
    protected ?Role $role = null;

    #[ORM\Column(enumType: GameRoleEnum::class)]
    protected GameRoleEnum $type;

    #[ORM\Column]
    protected bool $isSetup;

    public function __construct()
    {
        $this->isSetup = !$this->needSetup();
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getType(): ?GameRoleEnum
    {
        return $this->type ?? null;
    }

    public function setType(GameRoleEnum $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isSetup(): bool
    {
        return $this->isSetup;
    }

    public function setSetup(bool $isSetup): static
    {
        $this->isSetup = $isSetup;

        return $this;
    }

    abstract protected function needSetup(): bool;
}
