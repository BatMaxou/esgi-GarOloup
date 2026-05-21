<?php

namespace App\Tests\Helper\Builder\Workflow;

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

    public function __construct(
        private readonly NightWorkflowComposer $nightWorkflowComposer,
    ) {
    }

    public function forGame(GameBuilder $game): static
    {
        $this->game = $game;

        return $this;
    }

    protected function doBuild(): object
    {
        if (null === $this->game) {
            return WorkflowFactory::createOne();
        }

        $game = $this->game->getEntity();
        $workflow = $this->nightWorkflowComposer->for($game);
        $game->setWorkflow($workflow);

        return save($workflow);
    }
}
