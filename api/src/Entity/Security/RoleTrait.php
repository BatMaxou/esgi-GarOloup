<?php

namespace App\Entity\Security;

use App\Enum\RoleEnum;
use Doctrine\ORM\Mapping as ORM;

trait RoleTrait
{
    /** @var string[] */
    #[ORM\Column]
    private array $roles = [];

    abstract public function getDefaultRole(): RoleEnum;

    public function __construct()
    {
        $this->addRole($this->getDefaultRole());
    }

    /** @return string[] */
    public function getRoles(): array
    {
        $this->addRole($this->getDefaultRole());

        return $this->roles;
    }

    /** @param string[] $roles */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(RoleEnum $role): static
    {
        if (!in_array($role->value, $this->roles, true)) {
            $this->roles[] = $role->value;
        }

        return $this;
    }

    public function removeRole(RoleEnum $role): static
    {
        if (in_array($role->value, $this->roles, true)) {
            $this->roles = array_diff($this->roles, [$role->value]);
        }

        return $this;
    }

    public function hasRole(RoleEnum $role): bool
    {
        return in_array($role->value, $this->roles, true);
    }
}
