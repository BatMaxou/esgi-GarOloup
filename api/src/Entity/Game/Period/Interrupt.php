<?php

namespace App\Entity\Game\Period;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\InterruptAction;
use App\Entity\Game\Period\Interface\ActionPeriodInterface;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Game\Period\InterruptRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterruptRepository::class)]
class Interrupt implements ActionPeriodInterface
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(inversedBy: 'interrupts')]
    #[ORM\JoinColumn(nullable: false)]
    private Game $game;

    #[ORM\Column]
    private int $number;

    /** @var Collection<int, InterruptAction> */
    #[ORM\OneToMany(targetEntity: InterruptAction::class, mappedBy: 'interrupt', cascade: ['persist', 'remove'], orphanRemoval: true)]
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

    /**
     * @return Collection<int, InterruptAction>
     */
    public function getActions(): Collection
    {
        return $this->actions;
    }

    public function addAction(InterruptAction $action): static
    {
        if (!$this->actions->contains($action)) {
            $this->actions->add($action);
        }

        return $this;
    }
}
