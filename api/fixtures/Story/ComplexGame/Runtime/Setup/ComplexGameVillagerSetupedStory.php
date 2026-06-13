<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Setup;

use App\Fixtures\Story\ComplexGame\Initialisation\ComplexGameLaunchedStory;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\Game\Role\VillagerRoleBuilder;

class ComplexGameVillagerSetupedStory extends ComplexGameLaunchedStory
{
    public function execute(): void
    {
        parent::execute();

        $hostPlayerBuilder = $this->getState(self::HOST_PLAYER);
        \assert($hostPlayerBuilder instanceof PlayerBuilder);
        $hostPlayerId = $hostPlayerBuilder->getEntity()->getId();
        \assert(null !== $hostPlayerId);
        $friendPlayerId = $hostPlayerId->toString();

        foreach ($this->getPool(self::VILLAGERS_POOL) as $villagerPlayerBuilder) {
            \assert($villagerPlayerBuilder instanceof PlayerBuilder);
            $villagerRoleBuilder = $villagerPlayerBuilder->role;
            \assert($villagerRoleBuilder instanceof VillagerRoleBuilder);
            $villagerRoleBuilder->getEntity()->setFriendId($friendPlayerId);
        }

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-villager-setuped-';
    }
}
