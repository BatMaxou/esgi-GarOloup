<?php

namespace App\Api\Model\Recap;

/**
 * @phpstan-type ActionRecapArray array{actionType: string, source: string|null, targetPlayerId: string|null, secondaryPlayerId?: string|null, seenRole?: string|null}
 */
final readonly class ActionRecap
{
    public function __construct(
        public string $actionType,
        public ?string $source,
        public ?string $targetPlayerId,
        public ?string $secondaryPlayerId = null,
        public ?string $seenRole = null,
    ) {
    }

    /**
     * @return ActionRecapArray
     */
    public function toArray(): array
    {
        return [
            'actionType' => $this->actionType,
            'source' => $this->source,
            'targetPlayerId' => $this->targetPlayerId,
            'secondaryPlayerId' => $this->secondaryPlayerId,
            'seenRole' => $this->seenRole,
        ];
    }

    /**
     * @param ActionRecapArray $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            actionType: $data['actionType'],
            source: $data['source'] ?? null,
            targetPlayerId: $data['targetPlayerId'] ?? null,
            secondaryPlayerId: $data['secondaryPlayerId'] ?? null,
            seenRole: $data['seenRole'] ?? null,
        );
    }
}
