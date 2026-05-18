<?php

namespace App\Entity\Game;

use App\Entity\Game\NightAction\NightAction;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Game\NightRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NightRepository::class)]
class Night
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(inversedBy: 'nights')]
    #[ORM\JoinColumn(nullable: false)]
    private Game $game;

    #[ORM\Column]
    private int $number;

    #[ORM\Column]
    private bool $resolved = false;

    /** @var Collection<int, NightAction> */
    #[ORM\OneToMany(targetEntity: NightAction::class, mappedBy: 'night', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $actions;

    public function __construct(Game $game, int $number)
    {
        $this->game = $game;
        $this->number = $number;
        $this->actions = new ArrayCollection();
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function getNumber(): int
    {
        return $this->number;
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }

    public function setResolved(bool $resolved): static
    {
        $this->resolved = $resolved;

        return $this;
    }

    /**
     * @return Collection<int, NightAction>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(NightAction $action): static
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
        }

        return $this;
    }
}
