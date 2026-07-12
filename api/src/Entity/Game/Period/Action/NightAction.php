<?php

namespace App\Entity\Game\Period\Action;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\NightAction\CoupleDeathAction;
use App\Entity\Game\Period\Action\NightAction\InfectAction;
use App\Entity\Game\Period\Action\NightAction\MurderAction;
use App\Entity\Game\Period\Action\NightAction\RevealAction;
use App\Entity\Game\Period\Action\NightAction\SaveAction;
use App\Entity\Game\Period\Action\Trait\SourceActionTrait;
use App\Entity\Game\Period\Interface\PeriodAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Night;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use App\Repository\Game\Period\Action\NightActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: NightActionRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'murder' => MurderAction::class,
    'save' => SaveAction::class,
    'infection' => InfectAction::class,
    'reveal' => RevealAction::class,
    'couple_death' => CoupleDeathAction::class,
])]
abstract class NightAction implements PeriodAction
{
    use UuidTrait;
    use TimestampableTrait;
    use SourceActionTrait;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    protected Night $night;

    public function __construct(Night $night, GameRoleEnum|GameTeamEnum $source)
    {
        $this->night = $night;
        $this->setSource($source);
    }

    public function getNight(): Night
    {
        return $this->night;
    }

    /**
     * @param Night $period
     */
    abstract public function apply(PeriodInterface $period, Game $game): void;
}
