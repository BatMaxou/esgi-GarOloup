<?php

namespace App\Api\Model\Game;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\Game\Runtime\WerewolfTeamProvider;
use App\Entity\Game\Player;

#[ApiResource(
    operations: [
        new Get(
            name: 'api_current_werewolf_team',
            uriTemplate: '/game/werewolf-team',
            provider: WerewolfTeamProvider::class,
            normalizationContext: ['groups' => 'werewolf-team:read'],
        ),
    ],
)]
class WerewolfTeam
{
    /** @param Player[] $members */
    public function __construct(
        public string $gameId,
        public array $members,
    ) {
    }
}
