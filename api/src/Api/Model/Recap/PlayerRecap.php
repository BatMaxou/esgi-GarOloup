<?php

namespace App\Api\Model\Recap;

/**
 * @phpstan-type PlayerRecapArray array{playerId: string, userId: string, username: string, role: string|null, team: string|null, isInfected: bool, isInCouple: bool, isDead: bool}
 */
final readonly class PlayerRecap
{
    public function __construct(
        public string $playerId,
        public string $userId,
        public string $username,
        public ?string $role,
        public ?string $team,
        public bool $isInfected,
        public bool $isInCouple,
        public bool $isDead,
    ) {
    }

    /**
     * @return PlayerRecapArray
     */
    public function toArray(): array
    {
        return [
            'playerId' => $this->playerId,
            'userId' => $this->userId,
            'username' => $this->username,
            'role' => $this->role,
            'team' => $this->team,
            'isInfected' => $this->isInfected,
            'isInCouple' => $this->isInCouple,
            'isDead' => $this->isDead,
        ];
    }

    /**
     * @param PlayerRecapArray $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            playerId: $data['playerId'],
            userId: $data['userId'],
            username: $data['username'],
            role: $data['role'] ?? null,
            team: $data['team'] ?? null,
            isInfected: $data['isInfected'],
            isInCouple: $data['isInCouple'],
            isDead: $data['isDead'],
        );
    }
}
