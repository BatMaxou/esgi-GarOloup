<?php

namespace App\Entity\Game\Period\Vote;

use App\Entity\Game\Period\Vote;
use App\Entity\Game\Player;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Repository\Game\Period\Vote\BallotRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BallotRepository::class)]
class Ballot
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(inversedBy: 'ballots')]
    #[ORM\JoinColumn(nullable: false)]
    private Vote $vote;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Player $player;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Player $target;

    public function __construct(Vote $vote, Player $player, Player $target)
    {
        $this->vote = $vote;
        $this->player = $player;
        $this->target = $target;
    }

    public function getVote(): Vote
    {
        return $this->vote;
    }

    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function getTarget(): Player
    {
        return $this->target;
    }

    public function setTarget(Player $target): static
    {
        $this->target = $target;

        return $this;
    }
}
