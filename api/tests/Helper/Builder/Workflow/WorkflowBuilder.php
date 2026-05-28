<?php

namespace App\Tests\Helper\Builder\Workflow;

use App\Domain\Workflow\DayWorkflowComposer;
use App\Domain\Workflow\NightWorkflowComposer;
use App\Entity\Game\Workflow;
use App\Fixtures\Factory\Game\WorkflowFactory;
use App\Tests\Helper\Builder\AbstractBuilder;
use App\Tests\Helper\Builder\Game\GameBuilder;

use function Zenstruck\Foundry\Persistence\save;

/** @extends AbstractBuilder<Workflow> */
class WorkflowBuilder extends AbstractBuilder
{
    public ?GameBuilder $game = null;
    public ?bool $dayWorkflow = null;
    public ?bool $nightWorkflow = null;

    public function __construct(
        private readonly NightWorkflowComposer $nightWorkflowComposer,
        private readonly DayWorkflowComposer $dayWorkflowComposer,
    ) {
    }

    public function forGame(GameBuilder $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function day(): static
    {
        $this->dayWorkflow = true;

        return $this;
    }

    public function night(): static
    {
        $this->nightWorkflow = true;

        return $this;
    }

    protected function doBuild(): object
    {
        if (null === $this->game) {
            return WorkflowFactory::createOne();
        }

        $game = $this->game->getEntity();
        $workflow = null;
        if ($this->dayWorkflow) {
            $workflow = $this->dayWorkflowComposer->for($game);
            $game->setDayWorkflow($workflow);
        }

        if ($this->nightWorkflow) {
            $workflow = $this->nightWorkflowComposer->for($game);
            $game->setNightWorkflow($workflow);
        }

        if (!$workflow) {
            throw new \RuntimeException('No workflow type found');
        }

        return save($workflow);
    }
}
