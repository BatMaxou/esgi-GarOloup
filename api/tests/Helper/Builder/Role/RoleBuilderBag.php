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
            GameRoleEnum::SEER->value => (clone $this->builder)->seer(),
            GameRoleEnum::WITCH->value => (clone $this->builder)->witch(),
            GameRoleEnum::WILD_CHILD->value => (clone $this->builder)->wildChild(),
        ];

        return $this;
    }

    public function buildAll(): static
    {
        if (0 === \count($this->builders)) {
            $this->build();
        }

        foreach ($this->builders as $builder) {
            if (null !== $builder->tryGetEntity()) {
                continue;
            }

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

    public function getSeer(): RoleBuilder
    {
        return $this->get(GameRoleEnum::SEER);
    }

    public function getWitch(): RoleBuilder
    {
        return $this->get(GameRoleEnum::WITCH);
    }

    public function getWildChild(): RoleBuilder
    {
        return $this->get(GameRoleEnum::WILD_CHILD);
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
