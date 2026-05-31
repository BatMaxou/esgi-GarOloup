<?php

namespace App\Domain\Workflow;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Vote;
use App\Entity\Game\Player;
use App\Enum\Game\GameRuntimeStepEnum;
use Symfony\Component\Clock\ClockInterface;

class VoteResolver
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly int $voteDuration,
    ) {
    }

    public function start(Game $game): Vote
    {
        $vote = new Vote($game, $game->getVotes()->count() + 1);
        $game->addVote($vote);

        $game->setRuntimeStep(GameRuntimeStepEnum::VOTE);
        $game->setStepEndAt($this->clock->now()->modify(\sprintf('+%d seconds', $this->voteDuration)));

        return $vote;
    }

    public function resolve(Game $game): Game
    {
        $vote = $game->getCurrentVote() ?? throw new \LogicException('No active vote to resolve');

        $target = $this->resolveTarget($vote) ?? $this->getRandomAlivePlayer($game);
        $target?->setDead(true);

        $vote->setResolved(true);

        return $game;
    }

    private function resolveTarget(Vote $vote): ?Player
    {
        $tally = [];
        $targets = [];
        foreach ($vote->getBallots() as $ballot) {
            $target = $ballot->getTarget();
            $targetId = $target->getId()?->toString();
            if (null === $targetId) {
                continue;
            }

            $tally[$targetId] = ($tally[$targetId] ?? 0) + 1;
            $targets[$targetId] = $target;
        }

        if (empty($tally)) {
            return null;
        }

        $maxVotes = \max($tally);
        $topTargets = \array_keys(\array_filter($tally, fn (int $count) => $count === $maxVotes));

        return $targets[$topTargets[\array_rand($topTargets)]];
    }

    private function getRandomAlivePlayer(Game $game): ?Player
    {
        $candidates = [];
        foreach ($game->getPlayers() as $player) {
            if (!$player->isDead()) {
                $candidates[] = $player;
            }
        }

        if (empty($candidates)) {
            return null;
        }

        return $candidates[\array_rand($candidates)];
    }
}
