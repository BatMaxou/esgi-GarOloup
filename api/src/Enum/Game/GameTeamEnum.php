<?php

namespace App\Enum\Game;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Api\Provider\Filter\GameTeamFiltersProvider;
use App\Service\Filter\Interface\FilterAwareInterface;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/filters/game_teams',
            provider: GameTeamFiltersProvider::class,
            normalizationContext: ['groups' => ['filter:read']],
        ),
    ],
)]
enum GameTeamEnum: string implements FilterAwareInterface
{
    case VILLAGE = 'village';
    case WEREWOLF = 'werewolf';
    case SOLO = 'solo';
    case COUPLE = 'couple';

    public static function provideFilters(): array
    {
        return [
            self::VILLAGE->value,
            self::WEREWOLF->value,
            self::SOLO->value,
            self::COUPLE->value,
        ];
    }
}
