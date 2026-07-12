<?php

namespace App\Entity\Game\Period\Action;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\Trait\SourceActionTrait;
use App\Entity\Game\Period\Action\VoteAction\CoupleDeathAction;
use App\Entity\Game\Period\Interface\PeriodAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Vote;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use App\Repository\Game\Period\Action\VoteActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: VoteActionRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'couple_death' => CoupleDeathAction::class,
])]
abstract class VoteAction implements PeriodAction
{
    use UuidTrait;
    use TimestampableTrait;
    use SourceActionTrait;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    protected Vote $vote;

    public function __construct(Vote $vote, GameRoleEnum|GameTeamEnum $source)
    {
        $this->vote = $vote;
        $this->setSource($source);
    }

    public function getVote(): Vote
    {
        return $this->vote;
    }

    /**
     * @param Vote $period
     */
    abstract public function apply(PeriodInterface $period, Game $game): void;
}
