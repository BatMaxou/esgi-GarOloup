<?php

namespace App\Entity\Game\Period;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Vote\Ballot;
use App\Entity\Game\Player;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Game\Period\VoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote implements PeriodInterface
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private Game $game;

    #[ORM\Column]
    private int $number;

    #[ORM\Column]
    private bool $resolved = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Player $eliminatedPlayer = null;

    /** @var Collection<int, Ballot> */
    #[ORM\OneToMany(targetEntity: Ballot::class, mappedBy: 'vote', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $ballots;

    public function __construct(Game $game, int $number)
    {
        $this->game = $game;
        $this->number = $number;
        $this->ballots = new ArrayCollection();
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

    public function getEliminatedPlayer(): ?Player
    {
        return $this->eliminatedPlayer;
    }

    public function setEliminatedPlayer(?Player $eliminatedPlayer): static
    {
        $this->eliminatedPlayer = $eliminatedPlayer;

        return $this;
    }

    /**
     * @return Collection<int, Ballot>
     */
    public function getBallots(): Collection
    {
        return $this->ballots;
    }

    public function addBallot(Ballot $ballot): static
    {
        if (!$this->ballots->contains($ballot)) {
            $this->ballots->add($ballot);
        }

        return $this;
    }
}
