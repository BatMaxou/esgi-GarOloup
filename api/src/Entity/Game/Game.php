<?php

namespace App\Entity\Game;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Model\BasicActionOutput;
use App\Api\Model\Game\CreateGameOutput;
use App\Api\Provider\Game\CurrentGameProvider;
use App\Domain\Command\Game\Initialisation\CloseGameInvitationCommand;
use App\Domain\Command\Game\Initialisation\CreateGameCommand;
use App\Domain\Command\Game\Initialisation\GameRoleDispatchCommand;
use App\Domain\Command\Game\Initialisation\JoinGameCommand;
use App\Domain\Command\Game\Initialisation\LaunchGameCommand;
use App\Domain\Command\Game\Initialisation\ReOpenGameInvitationCommand;
use App\Domain\Command\Game\Initialisation\SetGameConfigurationCommand;
use App\Domain\Command\Game\Initialisation\SetGameMasterCommand;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameStepEnum;
use App\Repository\Game\GameRepository;
use App\Service\Mercure\Inteface\TopicRelatedObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            name: 'api_current_game',
            uriTemplate: '/game',
            provider: CurrentGameProvider::class,
            normalizationContext: [
                'groups' => 'game:read',
            ],
        ),
        new Post(
            name: 'api_game_create',
            messenger: 'input',
            input: CreateGameCommand::class,
            output: CreateGameOutput::class,
        ),
        new Post(
            name: 'api_game_join',
            uriTemplate: '/game/join',
            messenger: 'input',
            input: JoinGameCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(
            name: 'api_game_close_invitation',
            uriTemplate: '/game/invitation/close',
            messenger: 'input',
            input: CloseGameInvitationCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(
            name: 'api_game_reopen_invitation',
            uriTemplate: '/game/invitation/open',
            messenger: 'input',
            input: ReOpenGameInvitationCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(
            name: 'api_game_set_configuration',
            uriTemplate: '/game/configuration',
            messenger: 'input',
            input: SetGameConfigurationCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(
            name: 'api_game_set_game_master',
            uriTemplate: '/game/game-master',
            messenger: 'input',
            input: SetGameMasterCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(
            name: 'api_game_role_dispatch',
            uriTemplate: '/game/role-dispatch',
            messenger: 'input',
            input: GameRoleDispatchCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(
            name: 'api_game_launch',
            uriTemplate: '/game/launch',
            messenger: 'input',
            input: LaunchGameCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(),
    ],
)]
class Game implements TopicRelatedObject
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\Column(enumType: GameStepEnum::class)]
    private GameStepEnum $step;

    #[ORM\OneToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Player $host;

    #[ORM\OneToOne(inversedBy: 'managedGame', cascade: ['persist', 'remove'])]
    private ?Player $gameMaster = null;

    #[ORM\Column(length: 8)]
    private string $joinCode;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Configuration $configuration;

    /** @var Collection<int, Player> */
    #[ORM\OneToMany(targetEntity: Player::class, mappedBy: 'game')]
    private Collection $players;

    public function __construct(
        Player $host,
    ) {
        $this->step = GameStepEnum::NEW;
        $this->host = $host;

        $uuid = $this->generateUuid();
        $this->id = $uuid;
        $this->joinCode = substr($uuid->toBase32(), -8);

        $this->players = new ArrayCollection();
        $this->addPlayer($host);

        $this->configuration = new Configuration();
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function setConfiguration(Configuration $configuration): static
    {
        $this->configuration = $configuration;

        return $this;
    }

    public function getStep(): GameStepEnum
    {
        return $this->step;
    }

    public function setStep(GameStepEnum $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function getHost(): Player
    {
        return $this->host;
    }

    public function setHost(Player $host): static
    {
        $this->host = $host;

        return $this;
    }

    public function getGameMaster(): ?Player
    {
        return $this->gameMaster;
    }

    public function setGameMaster(?Player $gameMaster): static
    {
        $this->gameMaster = $gameMaster;

        if ($gameMaster) {
            $this->removePlayer($gameMaster);
            $gameMaster->setManagedGame($this);
        }

        return $this;
    }

    public function getJoinCode(): string
    {
        return $this->joinCode;
    }

    public function setJoinCode(string $joinCode): static
    {
        $this->joinCode = $joinCode;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): static
    {
        if (!$this->players->contains($player)) {
            $this->players->add($player);
            $player->setGame($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): static
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGame() === $this) {
                $player->setGame(null);
            }
        }

        return $this;
    }

    public function getTopicIdentifier(): ?string
    {
        return $this->getId();
    }
}
