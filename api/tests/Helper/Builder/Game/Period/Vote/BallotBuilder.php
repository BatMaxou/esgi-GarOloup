<?php

namespace App\Tests\Helper\Builder\Game\Period\Vote;

use App\Entity\Game\Period\Vote\Ballot;
use App\Fixtures\Factory\Game\Period\Vote\BallotFactory;
use App\Tests\Helper\Builder\AbstractBuilder;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;

/** @extends AbstractBuilder<Ballot> */
class BallotBuilder extends AbstractBuilder
{
    public ?GameBuilder $game = null;
    public ?PlayerBuilder $player = null;
    public ?PlayerBuilder $target = null;

    protected function doBuild(): object
    {
        $game = $this->game ?? throw new \LogicException('Ballot requires a game');
        $player = $this->player ?? throw new \LogicException('Ballot requires a voter');
        $target = $this->target ?? throw new \LogicException('Ballot requires a target');

        $vote = $game->getEntity()->getCurrentVote() ?? throw new \LogicException('Game has no active vote');

        $ballot = BallotFactory::new([
            'vote' => $vote,
            'player' => $player->getEntity(),
            'target' => $target->getEntity(),
        ])->withoutPersisting()->create();

        $vote->addBallot($ballot);

        return $ballot;
    }

    public function forGame(GameBuilder $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function by(PlayerBuilder $player): static
    {
        $this->player = $player;

        return $this;
    }

    public function against(PlayerBuilder $target): static
    {
        $this->target = $target;

        return $this;
    }
}
