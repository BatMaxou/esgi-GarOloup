<?php

namespace App\Entity\User;

use App\Enum\RoleEnum;
use App\Repository\User\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    public function getDefaultRole(): RoleEnum
    {
        return RoleEnum::ADMIN;
    }
}
