<?php

namespace App\Entity\Game;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Api\Model\BasicActionOutput;
use App\Api\Model\Game\CreateGameOutput;
use App\Api\Provider\Game\CurrentGameProvider;
use App\Api\Provider\Game\CurrentPublicGameProvider;
use App\Domain\Command\Game\Initialisation\CloseGameInvitationCommand;
use App\Domain\Command\Game\Initialisation\CreateGameCommand;
use App\Domain\Command\Game\Initialisation\GameRoleDispatchCommand;
use App\Domain\Command\Game\Initialisation\JoinGameCommand;
use App\Domain\Command\Game\Initialisation\LaunchGameCommand;
use App\Domain\Command\Game\Initialisation\ReOpenGameInvitationCommand;
use App\Domain\Command\Game\Initialisation\ResetConfigurationCommand;
use App\Domain\Command\Game\Initialisation\ResetGameMasterCommand;
use App\Domain\Command\Game\Initialisation\ResetRoleDispatchCommand;
use App\Domain\Command\Game\Initialisation\SetGameConfigurationCommand;
use App\Domain\Command\Game\Initialisation\SetGameMasterCommand;
use App\Domain\Command\Game\Runtime\TimeUpCommand;
use App\Domain\Command\Game\Runtime\VoteCommand;
use App\Entity\Game\Period\Day;
use App\Entity\Game\Period\Night;
use App\Entity\Game\Period\Vote;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameGlobalStepEnum;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
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
        new GetCollection(
            name: 'api_current_public_game',
            uriTemplate: '/games/public',
            provider: CurrentPublicGameProvider::class,
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
            name: 'api_game_reset_configuration',
            uriTemplate: '/game/configuration/reset',
            messenger: 'input',
            input: ResetConfigurationCommand::class,
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
            name: 'api_game_reset_game_master',
            uriTemplate: '/game/game-master/reset',
            messenger: 'input',
            input: ResetGameMasterCommand::class,
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
            name: 'api_game_reset_role_dispatch',
            uriTemplate: '/game/role-dispatch/reset',
            messenger: 'input',
            input: ResetRoleDispatchCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(
            name: 'api_game_launch',
            uriTemplate: '/game/launch',
            messenger: 'input',
            input: LaunchGameCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(
            name: 'api_game_vote',
            uriTemplate: '/game/vote',
            messenger: 'input',
            input: VoteCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(
            name: 'api_game_time_up',
            uriTemplate: '/game/time-up',
            messenger: 'input',
            input: TimeUpCommand::class,
            output: BasicActionOutput::class,
        ),
    ],
)]
class Game implements TopicRelatedObject
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\Column(enumType: GameInitialisationStepEnum::class)]
    private GameInitialisationStepEnum $initialisationStep;

    #[ORM\Column(enumType: GameRuntimeStepEnum::class, nullable: true)]
    private ?GameRuntimeStepEnum $runtimeStep = null;

    #[ORM\Column(enumType: GameRuntimeStepEnum::class, nullable: true)]
    private ?GameRuntimeStepEnum $interruptedRuntimeStep = null;

    #[ORM\Column(enumType: GameRoleEnum::class, nullable: true)]
    private ?GameRoleEnum $interruptedByRole = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $stepEndAt = null;

    #[ORM\Column(enumType: GameTeamEnum::class, nullable: true)]
    private ?GameTeamEnum $winningTeam = null;

    #[ORM\Column(enumType: GameRoleEnum::class, nullable: true)]
    private ?GameRoleEnum $winningRole = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Player $host;

    #[ORM\OneToOne(inversedBy: 'managedGame', cascade: ['persist', 'remove'])]
    private ?Player $gameMaster = null;

    #[ORM\Column(length: 8)]
    private string $joinCode;

    #[ORM\Column]
    private int $maxPlayers;

    #[ORM\Column]
    private int $maxTimeForDiscussion;

    #[ORM\Column]
    private bool $public = false;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Configuration $configuration;

    /** @var Collection<int, Player> */
    #[ORM\OneToMany(targetEntity: Player::class, mappedBy: 'game', cascade: ['remove'])]
    private Collection $players;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Workflow $nightWorkflow = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private ?Workflow $dayWorkflow = null;

    /** @var Collection<int, Night> */
    #[ORM\OneToMany(targetEntity: Night::class, mappedBy: 'game', cascade: ['persist', 'remove'])]
    private Collection $nights;

    /** @var Collection<int, Day> */
    #[ORM\OneToMany(targetEntity: Day::class, mappedBy: 'game', cascade: ['persist', 'remove'])]
    private Collection $days;

    /** @var Collection<int, Vote> */
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'game', cascade: ['persist', 'remove'])]
    private Collection $votes;

    public function __construct(
        Player $host,
    ) {
        $this->initialisationStep = GameInitialisationStepEnum::NEW;
        $this->host = $host;

        $uuid = $this->generateUuid();
        $this->id = $uuid;
        $this->joinCode = \substr($uuid->toBase32(), -8);

        $this->players = new ArrayCollection();
        $this->addPlayer($host);

        $this->configuration = new Configuration();
        $this->nights = new ArrayCollection();
        $this->days = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->nightWorkflow = new Workflow();
        $this->dayWorkflow = new Workflow();
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

    public function getInitialisationStep(): GameInitialisationStepEnum
    {
        return $this->initialisationStep;
    }

    public function setInitialisationStep(GameInitialisationStepEnum $initialisationStep): static
    {
        $this->initialisationStep = $initialisationStep;

        return $this;
    }

    public function getRuntimeStep(): ?GameRuntimeStepEnum
    {
        return $this->runtimeStep;
    }

    public function setRuntimeStep(?GameRuntimeStepEnum $runtimeStep): static
    {
        $this->runtimeStep = $runtimeStep;

        return $this;
    }

    public function getInterruptedRuntimeStep(): ?GameRuntimeStepEnum
    {
        return $this->interruptedRuntimeStep;
    }

    public function setInterruptedRuntimeStep(?GameRuntimeStepEnum $interruptedRuntimeStep): static
    {
        $this->interruptedRuntimeStep = $interruptedRuntimeStep;

        return $this;
    }

    public function getInterruptedByRole(): ?GameRoleEnum
    {
        return $this->interruptedByRole;
    }

    public function setInterruptedByRole(?GameRoleEnum $interruptedByRole): static
    {
        $this->interruptedByRole = $interruptedByRole;

        return $this;
    }

    public function getInterruptedByTeam(): ?GameTeamEnum
    {
        return $this->interruptedByRole?->getTeam();
    }

    public function getStepEndAt(): ?\DateTimeImmutable
    {
        return $this->stepEndAt;
    }

    public function setStepEndAt(?\DateTimeImmutable $stepEndAt): static
    {
        $this->stepEndAt = $stepEndAt;

        return $this;
    }

    public function getGlobalStep(): GameGlobalStepEnum
    {
        if (
            null !== $this->runtimeStep
            && GameRuntimeStepEnum::FINISH !== $this->runtimeStep
        ) {
            return GameGlobalStepEnum::RUNNING;
        }

        if (
            GameInitialisationStepEnum::FINISH === $this->initialisationStep
            && GameRuntimeStepEnum::FINISH === $this->runtimeStep
        ) {
            return GameGlobalStepEnum::FINISH;
        }

        return GameGlobalStepEnum::NEW;
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
            $gameMaster->setDead(true);
        }

        return $this;
    }

    public function removeGameMaster(): static
    {
        $gameMaster = $this->gameMaster;

        if (null !== $gameMaster) {
            $gameMaster->setManagedGame(null);
            $gameMaster->setDead(false);
            $this->setGameMaster(null);
            $this->addPlayer($gameMaster);
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

    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }

    public function setMaxPlayers(int $maxPlayers): static
    {
        $this->maxPlayers = $maxPlayers;

        return $this;
    }

    public function getMaxTimeForDiscussion(): int
    {
        return $this->maxTimeForDiscussion;
    }

    public function setMaxTimeForDiscussion(int $maxTimeForDiscussion): static
    {
        $this->maxTimeForDiscussion = $maxTimeForDiscussion;

        return $this;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): static
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return Collection<int, Player>
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function getPlayer(GameRoleEnum $type): ?Player
    {
        foreach ($this->players as $player) {
            if ($type === $player->getRole()?->getType()) {
                return $player;
            }
        }

        return null;
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
            if ($player->getGame() === $this) {
                $player->setGame(null);
            }
            // Flat indexes for this array
            $this->players = new ArrayCollection($this->players->getValues());
        }

        return $this;
    }

    public function getNightWorkflow(): ?Workflow
    {
        return $this->nightWorkflow;
    }

    public function setNightWorkflow(?Workflow $nightWorkflow): static
    {
        $this->nightWorkflow = $nightWorkflow;

        return $this;
    }

    public function getDayWorkflow(): ?Workflow
    {
        return $this->dayWorkflow;
    }

    public function setDayWorkflow(?Workflow $dayWorkflow): static
    {
        $this->dayWorkflow = $dayWorkflow;

        return $this;
    }

    /**
     * @return Collection<int, Night>
     */
    public function getNights(): Collection
    {
        return $this->nights;
    }

    public function addNight(Night $night): static
    {
        if (!$this->nights->contains($night)) {
            $this->nights->add($night);
        }

        return $this;
    }

    public function getCurrentNight(): ?Night
    {
        foreach ($this->nights as $night) {
            if (!$night->isResolved()) {
                return $night;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, Day>
     */
    public function getDays(): Collection
    {
        return $this->days;
    }

    public function addDay(Day $day): static
    {
        if (!$this->days->contains($day)) {
            $this->days->add($day);
        }

        return $this;
    }

    public function getCurrentDay(): ?Day
    {
        foreach ($this->days as $day) {
            if (!$day->isResolved()) {
                return $day;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
        }

        return $this;
    }

    public function getCurrentVote(): ?Vote
    {
        foreach ($this->votes as $vote) {
            if (!$vote->isResolved()) {
                return $vote;
            }
        }

        return null;
    }

    public function getTopicIdentifier(): ?string
    {
        return $this->getId();
    }

    public function getWinningTeam(): ?GameTeamEnum
    {
        return $this->winningTeam;
    }

    public function setWinningTeam(?GameTeamEnum $winningTeam): static
    {
        $this->winningTeam = $winningTeam;

        return $this;
    }

    public function getWinningRole(): ?GameRoleEnum
    {
        return $this->winningRole;
    }

    public function setWinningRole(?GameRoleEnum $winningRole): static
    {
        $this->winningRole = $winningRole;

        return $this;
    }

    public function countDeadPlayers(): int
    {
        return \count(\array_filter($this->players->toArray(), fn (Player $player) => $player->isDead()));
    }
}
