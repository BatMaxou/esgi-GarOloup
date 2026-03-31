<?php

namespace App\Entity\Game;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\Player\CurrentPlayerProvider;
use App\Entity\Game\Role\GameRole;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Entity\User\AbstractUser;
use App\Entity\User\TempUser;
use App\Entity\User\User;
use App\Repository\Game\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ApiResource(
    mercure: [
        'topics' => [
            '@=iri(object)',
        ],
    ],
    operations: [
        new Get(
            // set security here, currently used to map mercure topic to /players/:id
            name: 'api_get_player'
        ),
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
    private bool $dead = false;

    #[ORM\OneToOne(mappedBy: 'gameMaster')]
    private ?Game $managedGame = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    private ?Game $game = null;

    #[ORM\ManyToOne]
    private ?GameRole $role = null;

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
    }

    public function getLinkedUser(): AbstractUser
    {
        $user = $this->user ?? $this->tempUser;
        if (!$user) {
            throw new \LogicException('Player must have a linked user');
        }

        return $user;
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

    public function isDead(): bool
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

    public function getManagedGame(): ?Game
    {
        return $this->managedGame;
    }

    public function setManagedGame(?Game $managedGame): static
    {
        $this->managedGame = $managedGame;

        return $this;
    }

    public function isHost(): bool
    {
        return $this->game && $this->game->getHost() === $this;
    }

    public function isGameMaster(): bool
    {
        return $this->game && $this->game->getGameMaster() === $this;
    }

    public function getRole(): ?GameRole
    {
        return $this->role;
    }

    public function setRole(?GameRole $role): static
    {
        $this->role = $role;

        return $this;
    }
}
