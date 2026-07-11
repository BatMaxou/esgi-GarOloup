<?php

namespace App\Api\Model\Game;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\Game\Runtime\WerewolfTeamProvider;
use App\Entity\Game\Player;
use App\Service\Mercure\Inteface\TopicRelatedObject;

#[ApiResource(
    operations: [
        new Get(
            name: 'api_current_werewolf_team',
            uriTemplate: '/game/werewolf/team',
            provider: WerewolfTeamProvider::class,
            normalizationContext: ['groups' => 'werewolf-team:read'],
        ),
    ],
)]
class WerewolfTeam implements TopicRelatedObject
{
    /** @param Player[] $members */
    public function __construct(
        public string $gameId,
        public array $members,
    ) {
    }

    public function getTopicIdentifier(): ?string
    {
        return $this->gameId;
    }
}
