<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\TempUserProvider;
use App\Entity\Security\RoleTrait;
use App\Entity\Uuid\UuidTrait;
use App\Enum\RoleEnum;
use App\Repository\TempUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            name: 'api_temp_user_get',
            uriTemplate: '/temp_user',
            provider: TempUserProvider::class,
        ),
    ],
)]
#[ORM\Entity(repositoryClass: TempUserRepository::class)]
class TempUser implements UserInterface
{
    use UuidTrait;
    use RoleTrait {
        __construct as private initialiseRole;
    }

    #[ORM\Column(length: 255)]
    #[Assert\Ip]
    private ?string $ip = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    public function __construct(
        string $ip,
        string $username,
    ) {
        $this->id = $this->generateUuid();
        $this->ip = $ip;
        $this->username = $username;
        $this->initialiseRole();
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    public function eraseCredentials(): void
    {
    }

    public function getDefaultRole(): RoleEnum
    {
        return RoleEnum::TEMP_USER;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
}
