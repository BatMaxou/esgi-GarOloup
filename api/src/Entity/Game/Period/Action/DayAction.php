<?php

namespace App\Entity\Game\Period\Action;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\Trait\SourceActionTrait;
use App\Entity\Game\Period\Day;
use App\Entity\Game\Period\Interface\PeriodAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use App\Repository\Game\Period\Action\DayActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: DayActionRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([])]
abstract class DayAction implements PeriodAction
{
    use UuidTrait;
    use TimestampableTrait;
    use SourceActionTrait;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    protected Day $day;

    public function __construct(Day $day, GameRoleEnum|GameTeamEnum $source)
    {
        $this->day = $day;
        $this->setSource($source);
    }

    public function getDay(): Day
    {
        return $this->day;
    }

    /**
     * @param Day $period
     */
    abstract public function apply(PeriodInterface $period, Game $game): void;
}
