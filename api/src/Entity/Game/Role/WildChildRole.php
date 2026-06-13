<?php

namespace App\Entity\Game\Role;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Api\Model\BasicActionOutput;
use App\Domain\Command\Game\Runtime\WildChildSetupCommand;
use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Patch(
            name: 'api_game_wild_child_setup',
            uriTemplate: '/game/wild-child/setup',
            messenger: 'input',
            input: WildChildSetupCommand::class,
            output: BasicActionOutput::class,
        ),
    ],
)]
class WildChildRole extends GameRole implements NightResettableInterface, WerewolfVoterInterface
{
    #[ORM\Column(length: 36, nullable: true)]
    private ?string $modelPlayerId = null;

    #[ORM\Column]
    private bool $transformed = false;

    #[ORM\Column(length: 36, nullable: true)]
    private ?string $targetPlayerId = null;

    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::WILD_CHILD;
    }

    public function getModelPlayerId(): ?string
    {
        return $this->modelPlayerId;
    }

    public function setModelPlayerId(?string $modelPlayerId): static
    {
        $this->modelPlayerId = $modelPlayerId;

        return $this;
    }

    public function isTransformed(): bool
    {
        return $this->transformed;
    }

    public function transform(): static
    {
        $this->transformed = true;

        return $this;
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

    protected function needSetup(): bool
    {
        return true;
    }
}
