<?php

namespace App\Entity;

use App\Entity\Uuid\UuidTrait;
use App\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    use UuidTrait;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?TempUser $tempUser = null;

    #[ORM\Column]
    private ?bool $isDead = null;

    public function __construct(
        ?UserInterface $user = null,
    ) {
        match (true) {
            $user instanceof User => $this->user = $user,
            $user instanceof TempUser => $this->tempUser = $user,
            default => throw new \InvalidArgumentException('User not supported'),
        };
        $this->isDead = false;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTempUser(): ?TempUser
    {
        return $this->tempUser;
    }

    public function setTempUser(?TempUser $tempUser): static
    {
        $this->tempUser = $tempUser;

        return $this;
    }

    public function isDead(): ?bool
    {
        return $this->isDead;
    }

    public function setDead(bool $isDead): static
    {
        $this->isDead = $isDead;

        return $this;
    }
}
