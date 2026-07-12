<?php

namespace App\Entity\Game\Period;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\DayAction;
use App\Entity\Game\Period\Interface\ActionPeriodInterface;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Game\Period\DayRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DayRepository::class)]
class Day implements ActionPeriodInterface
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(inversedBy: 'days')]
    #[ORM\JoinColumn(nullable: false)]
    private Game $game;

    #[ORM\Column]
    private int $number;

    #[ORM\Column]
    private bool $resolved = false;

    /** @var Collection<int, DayAction> */
    #[ORM\OneToMany(targetEntity: DayAction::class, mappedBy: 'day', cascade: ['persist', 'remove'], orphanRemoval: true)]
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
     * @return Collection<int, DayAction>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(DayAction $action): static
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
        }

        return $this;
    }
}
