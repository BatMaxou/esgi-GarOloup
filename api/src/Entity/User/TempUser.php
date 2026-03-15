<?php

namespace App\Entity\User;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\User\MeProvider;
use App\Api\Provider\User\TempUserProvider;
use App\Enum\RoleEnum;
use App\Repository\User\TempUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            name: 'api_temp_user_get',
            uriTemplate: '/temp_user',
            provider: TempUserProvider::class,
        ),
        new Get(
            name: 'api_me',
            uriTemplate: '/me',
            provider: MeProvider::class,
            normalizationContext: [
                'groups' => 'me:read',
            ],
        ),
    ],
)]
#[ORM\Entity(repositoryClass: TempUserRepository::class)]
class TempUser extends AbstractUser
{
    #[ORM\Column(length: 255)]
    #[Assert\Ip]
    private string $ip;

    public function __construct(
        string $ip,
        string $username,
    ) {
        parent::__construct();

        $this->id = $this->generateUuid();
        $this->ip = $ip;
        $this->username = $username;
    }

    public function getUserIdentifier(): string
    {
        if (!$this->id) {
            throw new \LogicException('User id is not set.');
        }

        return $this->id->toString();
    }

    public function eraseCredentials(): void
    {
    }

    public function getDefaultRole(): RoleEnum
    {
        return RoleEnum::TEMP_USER;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
}
