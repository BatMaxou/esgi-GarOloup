<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Domain\Workflow\NightOrchestrator;
use App\Domain\Workflow\WorkflowBuilder;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\Game\Role\VillagerRoleBuilder;
use Doctrine\ORM\EntityManagerInterface;

class ClassicGameNight1WerewolfTurnStory extends ClassicGameConfiguredStory
{
    public function __construct(
        WorkflowBuilder $workflowBuilder,
        EntityManagerInterface $em,
        protected readonly NightOrchestrator $nightOrchestrator,
    ) {
        parent::__construct($workflowBuilder, $em);
    }

    public function build(): void
    {
        parent::build();

        $friendPlayerId = $this->getFriendPlayerId();
        $villagerRoleBuilders = $this->getVillagerRoleBuilders();
        foreach ($villagerRoleBuilders as $villagerRoleBuilder) {
            $villagerRoleBuilder->getEntity()->setFriendId($friendPlayerId);
        }

        $this->nightOrchestrator->startNight($this->getGameBuilder()->getEntity());
        $this->em->flush();
    }

    private function getFriendPlayerId(): string
    {
        $friendPlayerBuilder = $this->getState(self::HOST);
        \assert($friendPlayerBuilder instanceof PlayerBuilder);
        $friendPlayerId = $friendPlayerBuilder->getEntity()->getId();
        \assert(null !== $friendPlayerId);

        return $friendPlayerId->toString();
    }

    /**
     * @return VillagerRoleBuilder[]
     */
    private function getVillagerRoleBuilders(): array
    {
        $villager1PlayerBuilder = $this->getState(self::VILLAGER1);
        \assert($villager1PlayerBuilder instanceof PlayerBuilder);
        $villager1RoleBuilder = $villager1PlayerBuilder->role;
        \assert($villager1RoleBuilder instanceof VillagerRoleBuilder);

        $villager2PlayerBuilder = $this->getState(self::VILLAGER2);
        \assert($villager2PlayerBuilder instanceof PlayerBuilder);
        $villager2RoleBuilder = $villager2PlayerBuilder->role;
        \assert($villager2RoleBuilder instanceof VillagerRoleBuilder);

        $villager3PlayerBuilder = $this->getState(self::VILLAGER3);
        \assert($villager3PlayerBuilder instanceof PlayerBuilder);
        $villager3RoleBuilder = $villager3PlayerBuilder->role;
        \assert($villager3RoleBuilder instanceof VillagerRoleBuilder);

        $villager4PlayerBuilder = $this->getState(self::VILLAGER4);
        \assert($villager4PlayerBuilder instanceof PlayerBuilder);
        $villager4RoleBuilder = $villager4PlayerBuilder->role;
        \assert($villager4RoleBuilder instanceof VillagerRoleBuilder);

        return [$villager1RoleBuilder, $villager2RoleBuilder, $villager3RoleBuilder, $villager4RoleBuilder];
    }

    private function getGameBuilder(): GameBuilder
    {
        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        return $gameBuilder;
    }
}
