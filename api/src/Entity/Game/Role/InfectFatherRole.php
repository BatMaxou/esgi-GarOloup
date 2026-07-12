<?php

namespace App\Entity\Game\Role;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Api\Model\BasicActionOutput;
use App\Domain\Command\Game\Runtime\InfectCommand;
use App\Entity\Game\Role\Interface\PassableRoleInterface;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Patch(
            name: 'api_game_infect_father_infect',
            uriTemplate: '/game/infect-father/infect',
            messenger: 'input',
            input: InfectCommand::class,
            output: BasicActionOutput::class,
        ),
    ],
)]
class InfectFatherRole extends WerewolfRole implements PassableRoleInterface
{
    #[ORM\Column]
    private bool $infectionAvailable = true;

    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::INFECT_FATHER;
    }

    public function isInfectionAvailable(): bool
    {
        return $this->infectionAvailable;
    }

    public function useInfection(): static
    {
        $this->infectionAvailable = false;

        return $this;
    }
}
