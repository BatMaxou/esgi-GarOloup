<?php

namespace App\Service\Game;

use App\Api\Model\Recap\ActionRecap;
use App\Api\Model\Recap\BallotRecap;
use App\Api\Model\Recap\PeriodRecap;
use App\Api\Model\Recap\PlayerRecap;
use App\Entity\Game\Game;
use App\Entity\Game\Period\Day;
use App\Entity\Game\Period\Interface\PeriodAction;
use App\Entity\Game\Period\Interface\RevealedRoleActionInterface;
use App\Entity\Game\Period\Interface\SecondaryTargetableActionInterface;
use App\Entity\Game\Period\Interface\SeenRoleActionInterface;
use App\Entity\Game\Period\Interface\TargetableActionInterface;
use App\Entity\Game\Period\Interrupt;
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

        foreach ($game->getInterrupts() as $interrupt) {
            $items[] = [$interrupt->getCreatedAt(), $this->buildInterruptPeriod($interrupt)];
        }

        \usort($items, static fn (array $a, array $b): int => $a[0] <=> $b[0]);

        $periods = \array_column($items, 1);

        $setup = $this->buildSetupPeriod($game);
        if (null !== $setup) {
            \array_unshift($periods, $setup);
        }

        return $periods;
    }

    private function buildSetupPeriod(Game $game): ?PeriodRecap
    {
        $setup = $game->getSetup();
        if (null === $setup || $setup->getActions()->isEmpty()) {
            return null;
        }

        return new PeriodRecap(GameRuntimeStepEnum::SETUP, 0, $this->buildActions($setup->getActions()->toArray()), []);
    }

    private function buildNightPeriod(Night $night): PeriodRecap
    {
        return new PeriodRecap(GameRuntimeStepEnum::NIGHT, $night->getNumber(), $this->buildActions($night->getActions()->toArray()), []);
    }

    private function buildDayPeriod(Day $day): PeriodRecap
    {
        return new PeriodRecap(GameRuntimeStepEnum::DAY, $day->getNumber(), $this->buildActions($day->getActions()->toArray()), []);
    }

    private function buildInterruptPeriod(Interrupt $interrupt): PeriodRecap
    {
        return new PeriodRecap(GameRuntimeStepEnum::INTERRUPT, $interrupt->getNumber(), $this->buildActions($interrupt->getActions()->toArray()), []);
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
            $this->buildActions($vote->getActions()->toArray()),
            $ballots,
            eliminatedPlayerId: (string) $vote->getEliminatedPlayer()?->getId() ?: null,
        );
    }

    /**
     * @param PeriodAction[] $actions
     *
     * @return ActionRecap[]
     */
    private function buildActions(array $actions): array
    {
        return \array_map($this->buildActionRecap(...), $actions);
    }

    private function buildActionRecap(PeriodAction $action): ActionRecap
    {
        return new ActionRecap(
            actionType: $action->getType()->value,
            source: $action->getSource()?->value,
            targetPlayerId: $action instanceof TargetableActionInterface ? $action->getTargetPlayerId() : null,
            secondaryPlayerId: $action instanceof SecondaryTargetableActionInterface ? $action->getSecondaryPlayerId() : null,
            seenRole: $action instanceof SeenRoleActionInterface ? $action->getSeenRole()?->value : null,
            revealedRole: $action instanceof RevealedRoleActionInterface ? $action->getRevealedRole()?->value : null,
        );
    }
}
