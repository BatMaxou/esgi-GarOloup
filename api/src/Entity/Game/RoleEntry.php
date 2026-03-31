<?php

namespace App\Entity\Game;

use App\Entity\Role;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Game\RoleEntryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleEntryRepository::class)]
class RoleEntry
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(inversedBy: 'roles')]
    #[ORM\JoinColumn(nullable: false)]
    private Composition $composition;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Role $role;

    #[ORM\Column]
    private int $count;

    public function __construct(Composition $composition, Role $role, int $count)
    {
        $this->composition = $composition;
        $this->role = $role;
        $this->count = $count;
    }

    public function getComposition(): Composition
    {
        return $this->composition;
    }

    public function setComposition(Composition $composition): static
    {
        $this->composition = $composition;

        return $this;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }
}
