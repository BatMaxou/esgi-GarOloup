<?php

namespace App\Entity\Game\Role;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Api\Model\BasicActionOutput;
use App\Domain\Command\Game\Runtime\VillagerSetupCommand;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Patch(
            name: 'api_game_villager_setup',
            uriTemplate: '/game/villager/setup',
            messenger: 'input',
            input: VillagerSetupCommand::class,
            output: BasicActionOutput::class,
        ),
    ],
)]
class VillagerRole extends GameRole
{
    #[ORM\Column(nullable: true)]
    private ?string $friendId = null;

    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::VILLAGER;
    }

    public function getFriendId(): ?string
    {
        return $this->friendId;
    }

    public function setFriendId(?string $friendId): static
    {
        $this->friendId = $friendId;

        return $this;
    }

    protected function needSetup(): bool
    {
        return true;
    }
}
