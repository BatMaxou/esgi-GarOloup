<?php

namespace App\Entity\User;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Api\Model\BasicActionOutput;
use App\Api\Model\User\ForgotPasswordOutput;
use App\Api\Model\User\ResetPasswordOutput;
use App\Api\Provider\User\MeProvider;
use App\Domain\Command\User\ForgotPasswordCommand;
use App\Domain\Command\User\RegisterCommand;
use App\Domain\Command\User\ResetPasswordCommand;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/register',
            name: 'api_register',
            messenger: 'input',
            input: RegisterCommand::class,
            output: BasicActionOutput::class,
        ),
        new Post(
            uriTemplate: '/forgot_password',
            name: 'api_forgot_password',
            messenger: 'input',
            input: ForgotPasswordCommand::class,
            output: ForgotPasswordOutput::class,
            status: Response::HTTP_OK,
        ),
        new Post(
            uriTemplate: '/reset_password',
            name: 'api_reset_password',
            messenger: 'input',
            input: ResetPasswordCommand::class,
            output: ResetPasswordOutput::class,
            status: Response::HTTP_OK,
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
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User extends AbstractUser implements PasswordAuthenticatedUserInterface
{
    #[ORM\Column(length: 180)]
    private string $email;

    #[ORM\Column]
    private ?string $password = null; // @phpstan-ignore-line

    private ?string $plainPassword = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $resetToken = null;

    public function getEmail(): string
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
        if (empty($this->email)) {
            throw new \LogicException('User email is not set.');
        }

        return $this->email;
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

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        if ($this->password) {
            $data["\0".self::class."\0password"] = hash('crc32c', $this->password);
        }

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
