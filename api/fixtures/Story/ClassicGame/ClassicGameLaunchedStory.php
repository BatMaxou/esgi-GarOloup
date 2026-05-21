<?php

namespace App\Fixtures\Story\ClassicGame;

use App\Enum\Game\GameRuntimeStepEnum;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Workflow\WorkflowBuilder;
use App\Tests\Helper\ThereIs;

class ClassicGameLaunchedStory extends ClassicGameDispatchedStory
{
    public const WORKFLOW = 'workflow';

    public function build(): void
    {
        parent::build();

        $gameBuilder = $this->getState(self::GAME);
        \assert($gameBuilder instanceof GameBuilder);

        $workflowBuilder = ThereIs::aWorkflow($this->nightWorkflowComposer)->forGame($gameBuilder);
        $this->addState(self::WORKFLOW, $workflowBuilder);

        $gameBuilder->withRuntimeStep(GameRuntimeStepEnum::SETUP);
    }

    public function execute(): void
    {
        parent::execute();

        $workflowBuilder = $this->getState(self::WORKFLOW);
        \assert($workflowBuilder instanceof WorkflowBuilder);
        $workflowBuilder->build();
    }

    public function getPrefix(): string
    {
        return 'classic-game-launched-';
    }
}
