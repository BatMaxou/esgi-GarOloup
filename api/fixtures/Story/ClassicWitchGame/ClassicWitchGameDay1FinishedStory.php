<?php

namespace App\Fixtures\Story\ClassicWitchGame;

use App\Domain\Workflow\DayOrchestrator;
use App\Domain\Workflow\DayWorkflowComposer;
use App\Domain\Workflow\NightOrchestrator;
use App\Domain\Workflow\NightWorkflowComposer;
use App\Tests\Helper\Builder\Game\GameBuilder;
use Doctrine\ORM\EntityManagerInterface;

class ClassicWitchGameDay1FinishedStory extends ClassicWitchGameNight1WitchSavedStory
{
    public function __construct(
        NightWorkflowComposer $nightWorkflowComposer,
        DayWorkflowComposer $dayWorkflowComposer,
        EntityManagerInterface $em,
        NightOrchestrator $nightOrchestrator,
        protected readonly DayOrchestrator $dayOrchestrator,
    ) {
        parent::__construct($nightWorkflowComposer, $dayWorkflowComposer, $em, $nightOrchestrator);
    }

    public function execute(): void
    {
        parent::execute();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $game = $gameBuilder->getEntity();

        $this->dayOrchestrator->resolve($game);

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'classic-witch-game-day-1-finished-';
    }
}
