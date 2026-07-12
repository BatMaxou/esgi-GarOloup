<?php

namespace App\Service\Game\Period;

use App\Entity\Game\Game;
use App\Entity\Game\Period\Action\InterruptAction\CoupleDeathAction as InterruptCoupleDeathAction;
use App\Entity\Game\Period\Action\InterruptAction\HunterShotAction;
use App\Entity\Game\Period\Action\NightAction\CoupleDeathAction as NightCoupleDeathAction;
use App\Entity\Game\Period\Action\VoteAction\CoupleDeathAction as VoteCoupleDeathAction;
use App\Entity\Game\Period\Interface\PeriodAction;
use App\Entity\Game\Period\Interface\PeriodInterface;
use App\Entity\Game\Period\Interrupt;
use App\Entity\Game\Period\Night;
use App\Entity\Game\Period\Vote;
use App\Enum\Game\GameActionTypeEnum;
use App\Enum\Game\GameRuntimeStepEnum;

class PeriodActionFactory
{
    /**
     * @var array<string, array<class-string<PeriodInterface>, \Closure>>
     */
    private array $mapping;

    public function __construct()
    {
        $this->mapping = [
            GameActionTypeEnum::COUPLE_DEATH->value => [
                Night::class => static function (Night $period, string $targetPlayerId, string $secondaryPlayerId): PeriodAction {
                    $action = new NightCoupleDeathAction($period, $targetPlayerId, $secondaryPlayerId);
                    $period->addAction($action);

                    return $action;
                },
                Vote::class => static function (Vote $period, string $targetPlayerId, string $secondaryPlayerId): PeriodAction {
                    $action = new VoteCoupleDeathAction($period, $targetPlayerId, $secondaryPlayerId);
                    $period->addAction($action);

                    return $action;
                },
                Interrupt::class => static function (Interrupt $period, string $targetPlayerId, string $secondaryPlayerId): PeriodAction {
                    $action = new InterruptCoupleDeathAction($period, $targetPlayerId, $secondaryPlayerId);
                    $period->addAction($action);

                    return $action;
                },
            ],
            GameActionTypeEnum::HUNTER_SHOT->value => [
                Interrupt::class => static function (Interrupt $period, string $targetPlayerId): PeriodAction {
                    $action = new HunterShotAction($period, $targetPlayerId);
                    $period->addAction($action);

                    return $action;
                },
            ],
        ];
    }

    public function createForCurrentPeriod(Game $game, GameActionTypeEnum $type, string ...$arguments): ?PeriodAction
    {
        $period = $this->resolveCurrentPeriod($game);
        if (null === $period) {
            return null;
        }

        return $this->create($type, $period, ...$arguments);
    }

    public function create(GameActionTypeEnum $type, PeriodInterface $period, string ...$arguments): ?PeriodAction
    {
        foreach ($this->mapping[$type->value] ?? [] as $periodClass => $factory) {
            if ($period instanceof $periodClass) {
                $action = $factory($period, ...$arguments);
                \assert($action instanceof PeriodAction);

                return $action;
            }
        }

        return null;
    }

    public function resolveCurrentPeriod(Game $game): ?PeriodInterface
    {
        $period = match ($game->getRuntimeStep()) {
            GameRuntimeStepEnum::NIGHT => $game->getNights()->last(),
            GameRuntimeStepEnum::DAY => $game->getDays()->last(),
            GameRuntimeStepEnum::VOTE => $game->getVotes()->last(),
            GameRuntimeStepEnum::INTERRUPT => $game->getInterrupts()->last(),
            default => $game->getNights()->last(),
        };

        return $period instanceof PeriodInterface ? $period : null;
    }
}
