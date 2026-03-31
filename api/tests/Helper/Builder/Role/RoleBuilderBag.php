<?php

namespace App\Tests\Helper\Builder\Role;

use App\Enum\Game\GameRoleEnum;

class RoleBuilderBag
{
    /** @var array<string, RoleBuilder> */
    private array $builders = [];

    public function __construct(
        private readonly RoleBuilder $builder,
    ) {
    }

    public function build(): static
    {
        $this->builders = [
            GameRoleEnum::VILLAGER->value => (clone $this->builder)->villager(),
            GameRoleEnum::WEREWOLF->value => (clone $this->builder)->werewolf(),
        ];

        return $this;
    }

    public function buildAll(): static
    {
        if (0 === \count($this->builders)) {
            $this->build();
        }

        foreach ($this->builders as $builder) {
            $builder->build();
        }

        return $this;
    }

    public function getVillager(): RoleBuilder
    {
        return $this->get(GameRoleEnum::VILLAGER);
    }

    public function getWerewolf(): RoleBuilder
    {
        return $this->get(GameRoleEnum::WEREWOLF);
    }

    private function get(GameRoleEnum $role): RoleBuilder
    {
        if (!\array_key_exists($role->value, $this->builders)) {
            throw new \LogicException('Role do not exist');
        }

        $builder = $this->builders[$role->value];

        if ($builder->tryGetEntity()) {
            return $builder;
        }

        return $builder->build();
    }
}
