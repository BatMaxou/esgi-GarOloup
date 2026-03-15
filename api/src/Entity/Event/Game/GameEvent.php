<?php

namespace App\Entity\Event\Game;

use App\Entity\Game;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Entity\User\AbstractUser;
use App\Repository\Event\Game\GameEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameEventRepository::class)]
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

    public function getGameId(): string
    {
        return $this->gameId;
    }

    public function setGameId(string $gameId): static
    {
        $this->gameId = $gameId;

        return $this;
    }

    public function getPlayerUsername(): string
    {
        return $this->playerUsername;
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

        return $this;
    }

    public function getUser(): ?AbstractUser
    {
        return $this->user;
    }

    public function setUser(?AbstractUser $user): static
    {
        $this->user = $user;

        return $this;
    }
}
