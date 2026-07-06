<?php

namespace App\Fixtures\Story\ClassicGame\Runtime\Setup;

use App\Domain\Workflow\DayOrchestrator;
use App\Domain\Workflow\DayWorkflowComposer;
use App\Domain\Workflow\NightOrchestrator;
use App\Domain\Workflow\NightWorkflowComposer;
use App\Domain\Workflow\VoteResolver;
use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameLaunchedStory;
use App\Tests\Helper\Builder\Game\GameBuilder;
use Doctrine\ORM\EntityManagerInterface;

class ClassicGameSetupedStory extends ClassicGameLaunchedStory
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

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $this->nightOrchestrator->start($gameBuilder->getEntity());

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-game-setuped-';
    }
}
