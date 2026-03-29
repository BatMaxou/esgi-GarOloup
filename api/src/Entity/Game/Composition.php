<?php

namespace App\Entity\Game;

use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Game\CompositionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompositionRepository::class)]
class Composition
{
    use UuidTrait;
    use TimestampableTrait;

    /** @var Collection<int, RoleEntry> */
    #[ORM\OneToMany(targetEntity: RoleEntry::class, mappedBy: 'composition', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $roles;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * @return Collection<int, RoleEntry>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(RoleEntry $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(RoleEntry $role): static
    {
        $this->roles->removeElement($role);

        return $this;
    }
}
