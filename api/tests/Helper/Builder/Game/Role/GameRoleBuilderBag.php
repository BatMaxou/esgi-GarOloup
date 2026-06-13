<?php

namespace App\Tests\Helper\Builder\Game\Role;

use App\Entity\Game\Role\GameRole;
use App\Enum\Game\GameRoleEnum;
use App\Tests\Helper\Builder\Role\RoleBuilderBag;

class GameRoleBuilderBag
{
    /** @var array<string, \Closure(): GameRoleBuilder<covariant GameRole>> */
    private array $builders = [];

    public function __construct(
        private RoleBuilderBag $roleBuilderBag,
    ) {
    }

    public function build(): static
    {
        $this->builders = [
            GameRoleEnum::VILLAGER->value => fn () => new VillagerRoleBuilder($this->roleBuilderBag),
            GameRoleEnum::WEREWOLF->value => fn () => new WerewolfRoleBuilder($this->roleBuilderBag),
            GameRoleEnum::SEER->value => fn () => new SeerRoleBuilder($this->roleBuilderBag),
            GameRoleEnum::WITCH->value => fn () => new WitchRoleBuilder($this->roleBuilderBag),
            GameRoleEnum::WILD_CHILD->value => fn () => new WildChildRoleBuilder($this->roleBuilderBag),
        ];

        return $this;
    }

    public function getVillager(): VillagerRoleBuilder
    {
        return $this->get(GameRoleEnum::VILLAGER, VillagerRoleBuilder::class);
    }

    public function getWerewolf(): WerewolfRoleBuilder
    {
        return $this->get(GameRoleEnum::WEREWOLF, WerewolfRoleBuilder::class);
    }

    public function getSeer(): SeerRoleBuilder
    {
        return $this->get(GameRoleEnum::SEER, SeerRoleBuilder::class);
    }

    public function getWitch(): WitchRoleBuilder
    {
        return $this->get(GameRoleEnum::WITCH, WitchRoleBuilder::class);
    }

    public function getWildChild(): WildChildRoleBuilder
    {
        return $this->get(GameRoleEnum::WILD_CHILD, WildChildRoleBuilder::class);
    }

    /**
     * @template T of GameRoleBuilder
     *
     * @param class-string<T> $expected
     *
     * @return T
     */
    private function get(GameRoleEnum $role, string $expected): GameRoleBuilder
    {
        if (!\array_key_exists($role->value, $this->builders)) {
            throw new \LogicException('GameRole do not exist');
        }

        $factory = $this->builders[$role->value];
        $builder = $factory()->build();

        if (!$builder instanceof $expected) {
            throw new \LogicException('Unexpected builder type');
        }

        return $builder;
    }
}
