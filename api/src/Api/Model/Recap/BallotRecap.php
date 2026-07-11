<?php

namespace App\Api\Model\Recap;

/**
 * @phpstan-type BallotRecapArray array{playerId: string, targetPlayerId: string}
 */
final readonly class BallotRecap
{
    public function __construct(
        public string $playerId,
        public string $targetPlayerId,
    ) {
    }

    /**
     * @return BallotRecapArray
     */
    public function toArray(): array
    {
        return [
            'playerId' => $this->playerId,
            'targetPlayerId' => $this->targetPlayerId,
        ];
    }

    /**
     * @param BallotRecapArray $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            playerId: $data['playerId'],
            targetPlayerId: $data['targetPlayerId'],
        );
    }
}
