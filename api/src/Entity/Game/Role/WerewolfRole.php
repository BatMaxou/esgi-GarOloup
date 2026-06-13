<?php

namespace App\Entity\Game\Role;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Api\Model\BasicActionOutput;
use App\Domain\Command\Game\Runtime\WerewolfVoteCommand;
use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Patch(
            name: 'api_game_werewolf_vote',
            uriTemplate: '/game/werewolf/vote',
            messenger: 'input',
            input: WerewolfVoteCommand::class,
            output: BasicActionOutput::class,
        ),
    ],
)]
class WerewolfRole extends GameRole implements NightResettableInterface, WerewolfVoterInterface
{
    #[ORM\Column(length: 36, nullable: true)]
    private ?string $targetPlayerId = null;

    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::WEREWOLF;
    }

    protected function needSetup(): bool
    {
        return false;
    }

    public function getTargetPlayerId(): ?string
    {
        return $this->targetPlayerId;
    }

    public function setTargetPlayerId(?string $targetPlayerId): static
    {
        $this->targetPlayerId = $targetPlayerId;

        return $this;
    }

    public function clearNightState(): void
    {
        $this->targetPlayerId = null;
    }
}
