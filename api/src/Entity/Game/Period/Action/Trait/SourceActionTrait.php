<?php

namespace App\Entity\Game\Period\Action\Trait;

use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use Doctrine\ORM\Mapping as ORM;

trait SourceActionTrait
{
    #[ORM\Column(enumType: GameRoleEnum::class, nullable: true)]
    protected ?GameRoleEnum $roleSource = null;

    #[ORM\Column(enumType: GameTeamEnum::class, nullable: true)]
    protected ?GameTeamEnum $teamSource = null;

    protected function setSource(GameRoleEnum|GameTeamEnum $source): static
    {
        match (true) {
            $source instanceof GameRoleEnum => $this->roleSource = $source,
            $source instanceof GameTeamEnum => $this->teamSource = $source,
        };

        return $this;
    }

    public function getSource(): GameRoleEnum|GameTeamEnum|null
    {
        return $this->roleSource ?? $this->teamSource;
    }
}
