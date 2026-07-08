<?php

namespace App\Entity\Game\Role;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Api\Model\BasicActionOutput;
use App\Domain\Command\Game\Runtime\AssassinKillCommand;
use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Entity\Game\Role\Interface\NightKillImmuneInterface;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Patch(
            name: 'api_game_assassin_kill',
            uriTemplate: '/game/assassin/kill',
            messenger: 'input',
            input: AssassinKillCommand::class,
            output: BasicActionOutput::class,
        ),
    ],
)]
class AssassinRole extends GameRole implements NightResettableInterface, NightKillImmuneInterface
{
    #[ORM\Column]
    private bool $actedThisNight = false;

    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::ASSASSIN;
    }

    protected function needSetup(): bool
    {
        return false;
    }

    public function hasActedThisNight(): bool
    {
        return $this->actedThisNight;
    }

    public function markActedThisNight(): static
    {
        $this->actedThisNight = true;

        return $this;
    }

    public function clearNightState(): void
    {
        $this->actedThisNight = false;
    }

    public function isImmuneToNightMurder(GameRoleEnum $source): bool
    {
        return GameRoleEnum::WEREWOLF === $source;
    }
}
