<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Domain\Workflow\NightOrchestrator;
use App\Domain\Workflow\NightWorkflowComposer;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\Game\Role\VillagerRoleBuilder;
use Doctrine\ORM\EntityManagerInterface;

class ClassicGameSetupedStory extends ClassicGameLaunchedStory
{
    public function __construct(
        NightWorkflowComposer $nightWorkflowComposer,
        protected readonly EntityManagerInterface $em,
        protected readonly NightOrchestrator $nightOrchestrator,
    ) {
        parent::__construct($nightWorkflowComposer);
    }

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

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $this->nightOrchestrator->startNight($gameBuilder->getEntity());

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-game-setuped-';
    }
}
