<?php

namespace App\Api\Model\Recap;

/**
 * @phpstan-type ActionRecapArray array{actionType: string, source: string|null, targetPlayerId: string|null}
 */
final readonly class ActionRecap
{
    public function __construct(
        public string $actionType,
        public ?string $source,
        public ?string $targetPlayerId,
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
        );
    }
}
