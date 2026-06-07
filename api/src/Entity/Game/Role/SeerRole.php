<?php

namespace App\Entity\Game\Role;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Api\Model\BasicActionOutput;
use App\Domain\Command\Game\Runtime\SeerRevealCommand;
use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Entity\Game\Player;
use App\Enum\Game\GameRoleEnum;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Patch(
            name: 'api_game_seer_reveal',
            uriTemplate: '/game/seer/reveal',
            messenger: 'input',
            input: SeerRevealCommand::class,
            output: BasicActionOutput::class,
        ),
    ],
)]
class SeerRole extends GameRole implements NightResettableInterface
{
    #[ORM\Column(length: 36, nullable: true)]
    private ?string $lastObservedPlayerId = null;

    #[ORM\Column(enumType: GameRoleEnum::class, nullable: true)]
    private ?GameRoleEnum $lastObservedRole = null;

    /**
     * @var array<string, string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $observedRoles = [];

    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::SEER;
    }

    protected function needSetup(): bool
    {
        return false;
    }

    public function getLastObservedPlayerId(): ?string
    {
        return $this->lastObservedPlayerId;
    }

    public function setLastObservedPlayerId(?string $lastObservedPlayerId): static
    {
        $this->lastObservedPlayerId = $lastObservedPlayerId;

        return $this;
    }

    public function getLastObservedRole(): ?GameRoleEnum
    {
        return $this->lastObservedRole;
    }

    public function setLastObservedRole(?GameRoleEnum $lastObservedRole): static
    {
        $this->lastObservedRole = $lastObservedRole;

        return $this;
    }

    /**
     * @return array<string, GameRoleEnum>
     */
    public function getObservedRoles(): array
    {
        return \array_map(static fn (string $role) => GameRoleEnum::from($role), $this->observedRoles);
    }

    public function observe(Player $player): static
    {
        $playerId = $player->getId()?->toString();
        $role = $player->getRole()?->getType();
        if (null === $playerId || null === $role) {
            throw new \LogicException('Observed player must have an id and a role');
        }

        $this->lastObservedPlayerId = $playerId;
        $this->lastObservedRole = $role;
        $this->observedRoles[$playerId] = $role->value;

        return $this;
    }

    public function clearNightState(): void
    {
        $this->lastObservedPlayerId = null;
        $this->lastObservedRole = null;
    }
}
