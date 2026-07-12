<?php

namespace App\Entity\Game\Period\Action;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\SetupAction\CoupleAction;
use App\Entity\Game\Period\Action\SetupAction\WildChildModelAction;
use App\Entity\Game\Period\Action\Trait\SourceActionTrait;
use App\Entity\Game\Period\Interface\PeriodAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Setup;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use App\Repository\Game\Period\Action\SetupActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: SetupActionRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'wild_child_model' => WildChildModelAction::class,
    'couple' => CoupleAction::class,
])]
abstract class SetupAction implements PeriodAction
{
    use UuidTrait;
    use TimestampableTrait;
    use SourceActionTrait;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    protected Setup $setup;

    public function __construct(Setup $setup, GameRoleEnum|GameTeamEnum|null $source = null)
    {
        $this->setup = $setup;
        if (null !== $source) {
            $this->setSource($source);
        }
    }

    public function getSetup(): Setup
    {
        return $this->setup;
    }

    /**
     * @param Setup $period
     */
    public function apply(PeriodInterface $period, Game $game): void
    {
        // Setup actions only record what happened during the setup step; they have no effect.
    }
}
