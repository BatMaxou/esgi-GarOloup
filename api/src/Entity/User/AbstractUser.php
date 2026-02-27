<?php

namespace App\Entity\User;

use App\Entity\Trait\RoleTrait;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractUser implements UserInterface
{
    use UuidTrait;
    use TimestampableTrait;
    use RoleTrait {
        __construct as private initialiseRole;
    }

    #[ORM\Column(length: 255)]
    protected string $username;

    public function __construct()
    {
        $this->initialiseRole();
    }

    abstract public function getUserIdentifier(): string;

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }
}
