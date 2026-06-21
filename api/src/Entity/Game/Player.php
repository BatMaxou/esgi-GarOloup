<?php

namespace App\Entity\Game;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use App\Api\Processor\Game\LeaveGameProcessor;
use App\Api\Provider\Player\CurrentPlayerProvider;
use App\Entity\Game\Role\GameRole;
use App\Entity\Game\Role\Interface\WrapperRoleInterface;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Entity\User\AbstractUser;
use App\Entity\User\TempUser;
use App\Entity\User\User;
use App\Enum\Game\GameTeamEnum;
use App\Repository\Game\PlayerRepository;
use App\Service\Mercure\Inteface\TopicRelatedObject;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ApiResource(
    operations: [
        new Get(
            name: 'api_current_player',
            uriTemplate: '/game/player',
            provider: CurrentPlayerProvider::class,
            normalizationContext: [
                'groups' => 'me:player:read',
            ],
        ),
        new Delete(
            name: 'api_game_leave',
            uriTemplate: '/game/player',
            read: false,
            processor: LeaveGameProcessor::class,
        ),
    ],
)]
#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player implements TopicRelatedObject
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?TempUser $tempUser = null;

    #[ORM\Column]
    private bool $dead = false;

    #[ORM\Column]
    private int $afkCount = 0;

    #[ORM\OneToOne(mappedBy: 'gameMaster')]
    private ?Game $managedGame = null;

    #[ORM\ManyToOne(inversedBy: 'players')]
    private ?Game $game = null;

    #[ORM\ManyToOne]
    private ?GameRole $role = null;

    #[ORM\Column(enumType: GameTeamEnum::class, nullable: true)]
    private ?GameTeamEnum $team = null;

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

    public function getUsername(): string
    {
        return $this->getLinkedUser()->getUsername();
    }

    public function getLinkedGame(): ?Game
    {
        return $this->game ?? $this->managedGame;
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

    public function getAfkCount(): int
    {
        return $this->afkCount;
    }

    public function setAfkCount(int $afkCount): static
    {
        $this->afkCount = $afkCount;

        return $this;
    }

    public function incrementAfkCount(): static
    {
        ++$this->afkCount;

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
        $this->team = $role?->getType()?->getTeam();

        return $this;
    }

    /**
     * @template T of GameRole
     *
     * @param class-string<T> $class
     *
     * @return T|null
     */
    public function getRoleAs(string $class): ?GameRole
    {
        if ($this->role instanceof $class) {
            return $this->role;
        }

        if ($this->role instanceof WrapperRoleInterface && $this->role->getOriginalRole() instanceof $class) {
            return $this->role->getOriginalRole();
        }

        return null;
    }

    public function getTeam(): ?GameTeamEnum
    {
        return $this->team;
    }

    public function setTeam(?GameTeamEnum $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getTopicIdentifier(): ?string
    {
        return $this->getId();
    }
}
