<?php

namespace App\Entity\Game\Period\Action;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\InterruptAction\CoupleDeathAction;
use App\Entity\Game\Period\Action\InterruptAction\HunterShotAction;
use App\Entity\Game\Period\Action\Trait\SourceActionTrait;
use App\Entity\Game\Period\Interface\PeriodAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Interrupt;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use App\Repository\Game\Period\Action\InterruptActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: InterruptActionRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'hunter_shot' => HunterShotAction::class,
    'couple_death' => CoupleDeathAction::class,
])]
abstract class InterruptAction implements PeriodAction
{
    use UuidTrait;
    use TimestampableTrait;
    use SourceActionTrait;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    protected Interrupt $interrupt;

    public function __construct(Interrupt $interrupt, GameRoleEnum|GameTeamEnum $source)
    {
        $this->interrupt = $interrupt;
        $this->setSource($source);
    }

    public function getInterrupt(): Interrupt
    {
        return $this->interrupt;
    }

    /**
     * @param Interrupt $period
     */
    abstract public function apply(PeriodInterface $period, Game $game): void;
}
