<?php

namespace App\Tests\Helper\Builder\Role;

use App\Enum\Game\GameRoleEnum;

class RoleBuilderBag
{
    /** @var array<string, RoleBuilder> */
    private array $builders = [];

    public function __construct(RoleBuilder $builder)
    {
        $this->builders = [
            GameRoleEnum::VILLAGER->value => (clone $builder)->villager(),
            GameRoleEnum::WEREWOLF->value => (clone $builder)->werewolf(),
        ];
    }

    public function buildAll(): static
    {
        foreach ($this->builders as $builder) {
            $builder->build();
        }

        return $this;
    }

    public function buildVillager(): RoleBuilder
    {
        return $this->build(GameRoleEnum::VILLAGER)->build();
    }

    public function buildWerewolf(): RoleBuilder
    {
        return $this->build(GameRoleEnum::WEREWOLF)->build();
    }

    private function build(GameRoleEnum $role): RoleBuilder
    {
        if (!\array_key_exists($role->value, $this->builders)) {
            throw new \LogicException('Role do not exist');
        }

        return $this->builders[$role->value];
    }
}
