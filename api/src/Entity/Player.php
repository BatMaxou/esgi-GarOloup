<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\Player\CurrentPlayerProvider;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Entity\User\TempUser;
use App\Entity\User\User;
use App\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ApiResource(
    operations: [
        new Get(
            name: 'api_current_player',
            uriTemplate: '/game/player',
            provider: CurrentPlayerProvider::class,
            normalizationContext: [
                'groups' => 'me:read',
            ],
        ),
    ],
)]
#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?TempUser $tempUser = null;

    #[ORM\Column]
    private ?bool $dead = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    private ?Game $game = null;

    public function __construct(
        ?UserInterface $user = null,
    ) {
        if ($user) {
            match (true) {
                $user instanceof User => $this->user = $user,
                $user instanceof TempUser => $this->tempUser = $user,
                default => throw new \InvalidArgumentException('User not supported'),
            };
        }

        $this->dead = false;
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
        return $this->dead;
    }

    public function setDead(bool $isDead): static
    {
        $this->dead = $isDead;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }
}
