<?php

namespace App\Entity\User;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\User\MeProvider;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ApiResource(
    operations: [
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
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User extends AbstractUser implements PasswordAuthenticatedUserInterface
{
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    private ?string $plainPassword = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password, bool $alreadyHashed = false): static
    {
        if ($alreadyHashed) {
            $this->password = $password;
        } else {
            $this->plainPassword = $password;
            $this->password = null;
        }

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getDefaultRole(): RoleEnum
    {
        return RoleEnum::USER;
    }
}
