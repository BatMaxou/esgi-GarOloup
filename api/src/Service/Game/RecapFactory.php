<?php

namespace App\Service\Game;

use App\Api\Model\Recap\ActionRecap;
use App\Api\Model\Recap\BallotRecap;
use App\Api\Model\Recap\PeriodRecap;
use App\Api\Model\Recap\PlayerRecap;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\DayAction;
use App\Entity\Game\Period\Action\NightAction;
use App\Entity\Game\Period\Day;
use App\Entity\Game\Period\Interface\PeriodAction;
use App\Entity\Game\Period\Interface\TargetableActionInterface;
use App\Entity\Game\Period\Night;
use App\Entity\Game\Period\Vote;
use App\Entity\Game\Player;
use App\Entity\Game\Recap;
use App\Entity\Game\Role\InfectedRole;
use App\Entity\Game\Role\LoverRole;
use App\Enum\Game\GameRuntimeStepEnum;
use Doctrine\ORM\EntityManagerInterface;

class RecapFactory
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function createFromGame(Game $game): Recap
    {
        $winningTeam = $game->getWinningTeam() ?? throw new \LogicException('Cannot create a recap for an unfinished game');
        $recap = new Recap((string) $game->getId(), $winningTeam, $game->getWinningRole());
        $recap->setPlayers($this->buildPlayers($game));
        $recap->setPeriods($this->buildPeriods($game));

        $this->em->persist($recap);

        return $recap;
    }

    /** @return PlayerRecap[] */
    private function buildPlayers(Game $game): array
    {
        return \array_map(
            fn (Player $player) => $this->buildPlayerModel($player),
            $game->getPlayers()->toArray(),
        );
    }

    private function buildPlayerModel(Player $player): PlayerRecap
    {
        return new PlayerRecap(
            playerId: (string) $player->getId(),
            userId: (string) $player->getLinkedUser()->getId(),
            username: $player->getLinkedUser()->getUsername(),
            role: $player->getRole()?->getType()?->value,
            team: $player->getTeam()?->value,
            isInfected: null !== $player->getRoleAs(InfectedRole::class),
            isInCouple: null !== $player->getRoleAs(LoverRole::class),
            isDead: $player->isDead(),
        );
    }

    /** @return PeriodRecap[] */
    private function buildPeriods(Game $game): array
    {
        $items = [];

        foreach ($game->getNights() as $night) {
            $items[] = [$night->getCreatedAt(), $this->buildNightPeriod($night)];
        }

        foreach ($game->getDays() as $day) {
            $items[] = [$day->getCreatedAt(), $this->buildDayPeriod($day)];
        }

        foreach ($game->getVotes() as $vote) {
            $items[] = [$vote->getCreatedAt(), $this->buildVotePeriod($vote)];
        }

        \usort($items, static fn (array $a, array $b): int => $a[0] <=> $b[0]);

        return \array_column($items, 1);
    }

    private function buildNightPeriod(Night $night): PeriodRecap
    {
        $actions = \array_map(
            fn (NightAction $action) => new ActionRecap(
                actionType: $action->getType()->value,
                source: $action->getSource()?->value,
                targetPlayerId: $this->resolveTargetPlayerId($action),
            ),
            $night->getActions()->toArray(),
        );

        return new PeriodRecap(GameRuntimeStepEnum::NIGHT, $night->getNumber(), $actions, []);
    }

    private function buildDayPeriod(Day $day): PeriodRecap
    {
        $actions = \array_map(
            fn (DayAction $action) => new ActionRecap(
                actionType: $action->getType()->value,
                source: $action->getSource()?->value,
                targetPlayerId: $this->resolveTargetPlayerId($action),
            ),
            $day->getActions()->toArray(),
        );

        return new PeriodRecap(GameRuntimeStepEnum::DAY, $day->getNumber(), $actions, []);
    }

    private function resolveTargetPlayerId(PeriodAction $action): ?string
    {
        return $action instanceof TargetableActionInterface ? $action->getTargetPlayerId() : null;
    }

    private function buildVotePeriod(Vote $vote): PeriodRecap
    {
        $ballots = [];
        foreach ($vote->getBallots() as $ballot) {
            $ballots[] = new BallotRecap(
                playerId: (string) $ballot->getPlayer()->getId(),
                targetPlayerId: (string) $ballot->getTarget()->getId(),
            );
        }

        return new PeriodRecap(
            GameRuntimeStepEnum::VOTE,
            $vote->getNumber(),
            [],
            $ballots,
            eliminatedPlayerId: (string) $vote->getEliminatedPlayer()?->getId() ?: null,
        );
    }
}
