<?php

namespace App\Entity\Game;

use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameRoleEnum;
use App\Repository\Game\WorkflowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkflowRepository::class)]
class Workflow
{
    use UuidTrait;
    use TimestampableTrait;

    /** @var string[][] */
    #[ORM\Column(type: Types::JSON)]
    private array $steps = [];

    #[ORM\Column]
    private int $current = 0;

    /** @var string[] */
    #[ORM\Column(type: Types::JSON)]
    private array $currentTurn = [];

    #[ORM\Column]
    private bool $completed = false;

    /**
     * @return GameRoleEnum[][]
     */
    public function getSteps(): array
    {
        return \array_map(
            static fn (array $group) => \array_map(static fn (string $value) => GameRoleEnum::from($value), $group),
            $this->steps,
        );
    }

    /**
     * @return GameRoleEnum[]
     */
    public function getStepAt(int $index): array
    {
        if (!isset($this->steps[$index])) {
            return [];
        }

        return \array_map(static fn (string $value) => GameRoleEnum::from($value), $this->steps[$index]);
    }

    public function getCurrent(): int
    {
        return $this->current;
    }

    /**
     * @return GameRoleEnum[]
     */
    public function getCurrentTurn(): array
    {
        return \array_map(static fn (string $value) => GameRoleEnum::from($value), $this->currentTurn);
    }

    /**
     * @param GameRoleEnum[] $currentTurn
     */
    public function setCurrentTurn(array $currentTurn): static
    {
        $this->currentTurn = \array_map(static fn (GameRoleEnum $role) => $role->value, $currentTurn);

        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    public function nextStep(): static
    {
        if ($this->current + 1 >= \count($this->steps)) {
            $this->completed = true;

            return $this;
        }

        ++$this->current;

        return $this;
    }

    public function addStep(GameRoleEnum ...$roles): static
    {
        $this->steps[] = \array_map(static fn (GameRoleEnum $role) => $role->value, $roles);

        return $this;
    }

    public function reset(): static
    {
        $this->current = 0;
        $this->completed = empty($this->steps);
        $this->currentTurn = $this->steps[0] ?? [];

        return $this;
    }
}
