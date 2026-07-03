<?php

namespace App\Fixtures\Story\ComplexGame\Runtime\Setup;

use App\Domain\Workflow\DayOrchestrator;
use App\Domain\Workflow\DayWorkflowComposer;
use App\Domain\Workflow\NightOrchestrator;
use App\Domain\Workflow\NightWorkflowComposer;
use App\Domain\Workflow\VoteResolver;
use App\Entity\Game\Role\WildChildRole;
use App\Fixtures\Story\ComplexGame\Initialisation\ComplexGameLaunchedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\ClockInterface;

class ComplexGameSetupedStory extends ComplexGameLaunchedStory
{
    public function __construct(
        NightWorkflowComposer $nightWorkflowComposer,
        DayWorkflowComposer $dayWorkflowComposer,
        ClockInterface $clock,
        EntityManagerInterface $em,
        int $setupDuration,
        protected readonly NightOrchestrator $nightOrchestrator,
        protected readonly DayOrchestrator $dayOrchestrator,
        protected readonly VoteResolver $voteResolver,
    ) {
        parent::__construct($nightWorkflowComposer, $dayWorkflowComposer, $clock, $em, $setupDuration);
    }

    public function execute(): void
    {
        parent::execute();

        $modelPlayerBuilder = $this->getState($this->getWildChildModel());
        \assert($modelPlayerBuilder instanceof PlayerBuilder);
        $modelPlayerId = $modelPlayerBuilder->getEntity()->getId();
        \assert(null !== $modelPlayerId);

        $wildChildPlayerBuilder = $this->getState(self::WILD_CHILD);
        \assert($wildChildPlayerBuilder instanceof PlayerBuilder);
        $wildChildRole = $wildChildPlayerBuilder->getEntity()->getRole();
        \assert($wildChildRole instanceof WildChildRole);
        $wildChildRole->setModelPlayerId($modelPlayerId->toString());
        $wildChildRole->setSetup(true);

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $this->nightOrchestrator->start($gameBuilder->getEntity());

        $this->em->flush();
    }

    protected function getWildChildModel(): string
    {
        return self::HOST_PLAYER;
    }

    public function getPrefix(): string
    {
        return 'complex-game-setuped-';
    }
}
