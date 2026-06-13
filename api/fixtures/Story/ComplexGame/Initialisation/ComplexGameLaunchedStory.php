<?php

namespace App\Fixtures\Story\ComplexGame\Initialisation;

use App\Domain\Workflow\DayWorkflowComposer;
use App\Domain\Workflow\NightWorkflowComposer;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Workflow\WorkflowBuilder;
use App\Tests\Helper\ThereIs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Clock\ClockInterface;

class ComplexGameLaunchedStory extends ComplexGameDispatchedStory
{
    public const NIGHT_WORKFLOW = 'night-workflow';
    public const DAY_WORKFLOW = 'day-workflow';

    public function __construct(
        protected readonly NightWorkflowComposer $nightWorkflowComposer,
        protected readonly DayWorkflowComposer $dayWorkflowComposer,
        protected readonly ClockInterface $clock,
        protected readonly EntityManagerInterface $em,
        protected readonly int $setupDuration,
    ) {
    }

    public function build(): void
    {
        parent::build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $nightWorkflowBuilder = ThereIs::aWorkflow($this->nightWorkflowComposer, $this->dayWorkflowComposer)
            ->forGame($gameBuilder)
            ->night();
        $this->addState(self::NIGHT_WORKFLOW, $nightWorkflowBuilder);

        $dayWorkflowBuilder = ThereIs::aWorkflow($this->nightWorkflowComposer, $this->dayWorkflowComposer)
            ->forGame($gameBuilder)
            ->day();
        $this->addState(self::DAY_WORKFLOW, $dayWorkflowBuilder);

        $gameBuilder->withRuntimeStep(GameRuntimeStepEnum::SETUP);
    }

    public function execute(): void
    {
        parent::execute();

        $nightWorkflowBuilder = $this->getState(self::NIGHT_WORKFLOW);
        \assert($nightWorkflowBuilder instanceof WorkflowBuilder);
        $nightWorkflowBuilder->build();

        $dayWorkflowBuilder = $this->getState(self::DAY_WORKFLOW);
        \assert($dayWorkflowBuilder instanceof WorkflowBuilder);
        $dayWorkflowBuilder->build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);
        $gameBuilder->getEntity()->setStepEndAt(
            $this->clock->now()->modify(\sprintf('+%d seconds', $this->setupDuration)),
        );

        $this->em->flush();
    }

    public function getPrefix(): string
    {
        return 'complex-game-launched-';
    }
}
