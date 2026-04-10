<?php

namespace App\Entity\User;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\RoleEnum;
use App\Repository\User\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
#[ApiResource(operations: [])]
class Admin extends User
{
    public function getDefaultRole(): RoleEnum
    {
        return RoleEnum::ADMIN;
    }
}
