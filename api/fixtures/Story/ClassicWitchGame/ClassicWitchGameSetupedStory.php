<?php

namespace App\Fixtures\Story\ClassicWitchGame;

use App\Domain\Workflow\DayOrchestrator;
use App\Domain\Workflow\DayWorkflowComposer;
use App\Domain\Workflow\NightOrchestrator;
use App\Domain\Workflow\NightWorkflowComposer;
use App\Domain\Workflow\VoteResolver;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\Game\Role\VillagerRoleBuilder;
use Doctrine\ORM\EntityManagerInterface;

class ClassicWitchGameSetupedStory extends ClassicWitchGameLaunchedStory
{
    public function __construct(
        NightWorkflowComposer $nightWorkflowComposer,
        DayWorkflowComposer $dayWorkflowComposer,
        protected readonly EntityManagerInterface $em,
        protected readonly NightOrchestrator $nightOrchestrator,
        protected readonly DayOrchestrator $dayOrchestrator,
        protected readonly VoteResolver $voteResolver,
    ) {
        parent::__construct($nightWorkflowComposer, $dayWorkflowComposer);
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
        $this->nightOrchestrator->start($gameBuilder->getEntity());

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-witch-game-setuped-';
    }
}
