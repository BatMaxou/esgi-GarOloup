<?php

namespace App\Entity\Event\Game;

use App\Entity\Game\Game;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Entity\User\AbstractUser;
use App\Repository\Event\Game\GameEventRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: GameEventRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'close_invitation' => CloseGameInvitationEvent::class,
    'create' => CreateGameEvent::class,
    'role_dispatch' => GameRoleDispatchEvent::class,
    'join' => JoinGameEvent::class,
    'leave' => LeaveGameEvent::class,
    're_open_invitation' => ReOpenGameInvitationEvent::class,
    'reset_configuration' => ResetConfigurationEvent::class,
    'reset_game_master' => ResetGameMasterEvent::class,
    'reset_role_dispatch' => ResetRoleDispatchEvent::class,
    'set_configuration' => SetGameConfigurationEvent::class,
    'launch' => LaunchGameEvent::class,
    'set_game_master' => SetGameMasterEvent::class,
    'seer_reveal' => SeerRevealEvent::class,
    'time_up' => TimeUpGameEvent::class,
    'villager_setup' => VillagerSetupEvent::class,
    'vote' => VoteEvent::class,
    'werewolf_vote' => WerewolfVoteEvent::class,
    'witch_save' => WitchSaveEvent::class,
    'witch_poison' => WitchPoisonEvent::class,
    'wild_child_setup' => WildChildSetupEvent::class,
    'hunter_shoot' => HunterShootEvent::class,
    'infect' => InfectEvent::class,
])]
abstract class GameEvent
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\Column(length: 255)]
    private string $gameId;

    #[ORM\Column(length: 255)]
    private string $playerUsername;

    private ?Game $game = null;
    private ?AbstractUser $user = null;

    public function getGameId(): ?string
    {
        return $this->gameId ?? null;
    }

    public function setGameId(string $gameId): static
    {
        $this->gameId = $gameId;

        return $this;
    }

    public function getPlayerUsername(): ?string
    {
        return $this->playerUsername ?? null;
    }

    public function setPlayerUsername(string $playerUsername): static
    {
        $this->playerUsername = $playerUsername;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;
        if ($id = $game?->getId()) {
            $this->gameId = $id;
        }

        return $this;
    }

    public function getUser(): ?AbstractUser
    {
        return $this->user;
    }

    public function setUser(?AbstractUser $user): static
    {
        $this->user = $user;
        if ($usrname = $user?->getUsername()) {
            $this->playerUsername = $usrname;
        }

        return $this;
    }
}
