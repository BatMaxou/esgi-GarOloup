<?php

namespace App\Entity\Game\Role;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Patch;
use App\Api\Model\BasicActionOutput;
use App\Domain\Command\Game\Runtime\WitchPoisonCommand;
use App\Domain\Command\Game\Runtime\WitchSaveCommand;
use App\Domain\Workflow\Interface\NightResettableInterface;
use App\Entity\Game\Role\Interface\PassableRoleInterface;
use App\Enum\Game\GameRoleEnum;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Patch(
            name: 'api_game_witch_save',
            uriTemplate: '/game/witch/save',
            messenger: 'input',
            input: WitchSaveCommand::class,
            output: BasicActionOutput::class,
        ),
        new Patch(
            name: 'api_game_witch_poison',
            uriTemplate: '/game/witch/poison',
            messenger: 'input',
            input: WitchPoisonCommand::class,
            output: BasicActionOutput::class,
        ),
    ],
)]
class WitchRole extends GameRole implements NightResettableInterface, PassableRoleInterface
{
    #[ORM\Column]
    private bool $healPotionAvailable = true;

    #[ORM\Column]
    private bool $poisonPotionAvailable = true;

    #[ORM\Column]
    private bool $actedThisNight = false;

    public function __construct()
    {
        parent::__construct();
        $this->type = GameRoleEnum::WITCH;
    }

    protected function needSetup(): bool
    {
        return false;
    }

    public function isHealPotionAvailable(): bool
    {
        return $this->healPotionAvailable;
    }

    public function isPoisonPotionAvailable(): bool
    {
        return $this->poisonPotionAvailable;
    }

    public function hasActedThisNight(): bool
    {
        return $this->actedThisNight;
    }

    public function useHealPotion(): static
    {
        $this->healPotionAvailable = false;
        $this->actedThisNight = true;

        return $this;
    }

    public function usePoisonPotion(): static
    {
        $this->poisonPotionAvailable = false;
        $this->actedThisNight = true;

        return $this;
    }

    public function clearNightState(): void
    {
        $this->actedThisNight = false;
    }
}
