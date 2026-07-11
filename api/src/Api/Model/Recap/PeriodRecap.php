<?php

namespace App\Api\Model\Recap;

use App\Enum\Game\GameRuntimeStepEnum;

/**
 * @phpstan-import-type ActionRecapArray from ActionRecap
 * @phpstan-import-type BallotRecapArray from BallotRecap
 *
 * @phpstan-type PeriodRecapArray array{type: string, number: int, actions: array<ActionRecapArray>, ballots: array<BallotRecapArray>, eliminatedPlayerId: string|null}
 */
final readonly class PeriodRecap
{
    /**
     * @param ActionRecap[] $actions
     * @param BallotRecap[] $ballots
     */
    public function __construct(
        public GameRuntimeStepEnum $type,
        public int $number,
        public array $actions,
        public array $ballots,
        public ?string $eliminatedPlayerId = null,
    ) {
    }

    /**
     * @return PeriodRecapArray
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'number' => $this->number,
            'actions' => \array_map(fn (ActionRecap $a) => $a->toArray(), $this->actions),
            'ballots' => \array_map(fn (BallotRecap $b) => $b->toArray(), $this->ballots),
            'eliminatedPlayerId' => $this->eliminatedPlayerId,
        ];
    }

    /**
     * @param PeriodRecapArray $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: GameRuntimeStepEnum::from($data['type']),
            number: $data['number'],
            actions: \array_map(ActionRecap::fromArray(...), $data['actions']),
            ballots: \array_map(BallotRecap::fromArray(...), $data['ballots']),
            eliminatedPlayerId: $data['eliminatedPlayerId'] ?? null,
        );
    }
}
