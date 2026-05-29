<?php

namespace App\Entity\Game;

use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Game\ConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConfigurationRepository::class)]
class Configuration
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\OneToOne(mappedBy: 'configuration')]
    private ?Game $game = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Composition $composition = null;

    #[ORM\Column]
    private bool $withGameMaster = false;

    #[ORM\Column]
    private bool $withRandomDispatch = true;

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        if (null !== $game && $game->getConfiguration() !== $this) {
            $game->setConfiguration($this);
        }

        return $this;
    }

    public function getComposition(): ?Composition
    {
        return $this->composition;
    }

    public function setComposition(?Composition $composition): static
    {
        $this->composition = $composition;

        return $this;
    }

    public function isWithGameMaster(): bool
    {
        return $this->withGameMaster;
    }

    public function setWithGameMaster(bool $withGameMaster): static
    {
        $this->withGameMaster = $withGameMaster;

        return $this;
    }

    public function isWithRandomDispatch(): bool
    {
        return $this->withRandomDispatch;
    }

    public function setWithRandomDispatch(bool $withRandomDispatch): static
    {
        $this->withRandomDispatch = $withRandomDispatch;

        return $this;
    }
}
