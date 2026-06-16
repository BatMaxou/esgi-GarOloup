<?php

namespace App\Entity\Game\Role;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Api\Model\BasicActionOutput;
use App\Domain\Command\Game\Runtime\HunterShootCommand;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Patch(
            name: 'api_game_hunter_shoot',
            uriTemplate: '/game/hunter/shoot',
            messenger: 'input',
            input: HunterShootCommand::class,
            output: BasicActionOutput::class,
        ),
    ],
)]
class HunterRole extends GameRole
{
    #[ORM\Column]
    private bool $hasShot = false;

    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::HUNTER;
    }

    protected function needSetup(): bool
    {
        return false;
    }

    public function hasShot(): bool
    {
        return $this->hasShot;
    }

    public function markShot(): static
    {
        $this->hasShot = true;

        return $this;
    }
}
