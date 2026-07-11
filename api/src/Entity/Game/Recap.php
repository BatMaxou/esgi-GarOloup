<?php

namespace App\Entity\Game;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\Api\Model\Recap\PeriodRecap;
use App\Api\Model\Recap\PlayerRecap;
use App\Api\Provider\Game\GameRecapProvider;
use App\Entity\Trait\TimestampableTrait;
use App\Entity\Trait\UuidTrait;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameTeamEnum;
use App\Repository\Game\RecapRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @phpstan-import-type PlayerRecapArray from PlayerRecap
 * @phpstan-import-type PeriodRecapArray from PeriodRecap
 */
#[ORM\Entity(repositoryClass: RecapRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            name: 'api_game_recap',
            uriTemplate: '/games/{gameId}/recap',
            uriVariables: [
                'gameId' => new Link(fromClass: Recap::class, identifiers: ['gameId']),
            ],
            provider: GameRecapProvider::class,
            normalizationContext: ['groups' => 'recap:read'],
        ),
    ],
)]
class Recap
{
    use UuidTrait;
    use TimestampableTrait;

    #[ORM\Column(length: 36)]
    private string $gameId;

    #[ORM\Column(enumType: GameTeamEnum::class)]
    private GameTeamEnum $winningTeam;

    #[ORM\Column(enumType: GameRoleEnum::class, nullable: true)]
    private ?GameRoleEnum $winningRole;

    /** @var array<PlayerRecapArray> */
    #[ORM\Column(type: 'json')]
    private array $players = [];

    /** @var array<PeriodRecapArray> */
    #[ORM\Column(type: 'json')]
    private array $periods = [];

    public function __construct(string $gameId, GameTeamEnum $winningTeam, ?GameRoleEnum $winningRole = null)
    {
        $this->gameId = $gameId;
        $this->winningTeam = $winningTeam;
        $this->winningRole = $winningRole;
    }

    public function getGameId(): string
    {
        return $this->gameId;
    }

    public function getWinningTeam(): GameTeamEnum
    {
        return $this->winningTeam;
    }

    public function getWinningRole(): ?GameRoleEnum
    {
        return $this->winningRole;
    }

    /**
     * @return PlayerRecap[]
     */
    public function getPlayers(): array
    {
        return \array_map(PlayerRecap::fromArray(...), $this->players);
    }

    /**
     * @param PlayerRecap[] $players
     */
    public function setPlayers(array $players): static
    {
        $this->players = \array_map(
            fn (PlayerRecap $model) => $model->toArray(),
            $players,
        );

        return $this;
    }

    /**
     * @return PeriodRecap[]
     */
    public function getPeriods(): array
    {
        return \array_map(PeriodRecap::fromArray(...), $this->periods);
    }

    /**
     * @param PeriodRecap[] $periods
     */
    public function setPeriods(array $periods): static
    {
        $this->periods = \array_map(
            fn (PeriodRecap $model) => $model->toArray(),
            $periods,
        );

        return $this;
    }
}
