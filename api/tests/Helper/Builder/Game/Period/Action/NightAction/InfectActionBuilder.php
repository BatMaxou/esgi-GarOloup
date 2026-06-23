<?php

namespace App\Tests\Helper\Builder\Game\Period\Action\NightAction;

use App\Entity\Game\Period\Action\NightAction\InfectAction;
use App\Enum\Game\GameRoleEnum;
use App\Fixtures\Factory\Game\Period\Action\NightAction\InfectActionFactory;
use App\Tests\Helper\Builder\AbstractBuilder;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

/** @extends AbstractBuilder<InfectAction> */
class InfectActionBuilder extends AbstractBuilder
{
    public ?GameBuilder $game = null;
    public GameRoleEnum $source = GameRoleEnum::INFECT_FATHER;
    public ?PlayerBuilder $target = null;

    protected function doBuild(): object
    {
        $game = $this->game ?? throw new \LogicException('InfectAction requires a game');
        $target = $this->target ?? throw new \LogicException('InfectAction requires a target');

        $night = $game->getEntity()->getCurrentNight() ?? throw new \LogicException('Game has no active night');
        $targetId = $target->getEntity()->getId()?->toString()
            ?? throw new \LogicException('Target player id should not be null');

        $action = InfectActionFactory::createOne([
            'night' => $night,
            'source' => $this->source,
            'targetPlayerId' => $targetId,
        ]);

        $night->addAction($action);

        return $action;
    }

    public function forGame(GameBuilder $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function from(GameRoleEnum $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function against(PlayerBuilder $target): static
    {
        $this->target = $target;

        return $this;
    }
}
