<?php

namespace App\Entity\Game\Role;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Api\Model\BasicActionOutput;
use App\Domain\Command\Game\Runtime\CupidonSetupCommand;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Patch(
            name: 'api_game_cupidon_setup',
            uriTemplate: '/game/cupidon/setup',
            messenger: 'input',
            input: CupidonSetupCommand::class,
            output: BasicActionOutput::class,
        ),
    ],
)]
class CupidonRole extends GameRole
{
    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::CUPIDON;
    }

    protected function needSetup(): bool
    {
        return true;
    }
}
