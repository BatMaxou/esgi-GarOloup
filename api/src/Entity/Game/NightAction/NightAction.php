<?php

namespace App\Entity\Game\NightAction;

use App\Entity\Game\Game;
use App\Entity\Game\Night;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameRoleEnum;
use App\Repository\Game\NightAction\NightActionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;

#[ORM\Entity(repositoryClass: NightActionRepository::class)]
#[InheritanceType('JOINED')]
#[DiscriminatorColumn(name: 'discr', type: 'string')]
#[DiscriminatorMap([
    'murder' => MurderAction::class,
])]
abstract class NightAction
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\ManyToOne(inversedBy: 'actions')]
    #[ORM\JoinColumn(nullable: false)]
    protected Night $night;

    #[ORM\Column(enumType: GameRoleEnum::class)]
    protected GameRoleEnum $source;

    public function __construct(Night $night, GameRoleEnum $source)
    {
        $this->night = $night;
        $this->source = $source;
    }

    public function getNight(): Night
    {
        return $this->night;
    }

    public function getSource(): GameRoleEnum
    {
        return $this->source;
    }

    abstract public function apply(Night $night, Game $game): void;
}
