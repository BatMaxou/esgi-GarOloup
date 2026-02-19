<?php

namespace App\Entity;

use App\Entity\Security\RoleTrait;
use App\Entity\Uuid\UuidTrait;
use App\Enum\RoleEnum;
use App\Repository\TempUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: TempUserRepository::class)]
class TempUser implements UserInterface
{
    use UuidTrait;
    use RoleTrait {
        __construct as private initialiseRole;
    }

    public function __construct()
    {
        $this->id = Uuid::v7();
        $this->initialiseRole();
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getDefaultRole(): RoleEnum
    {
        return RoleEnum::TEMP_USER;
    }
}
