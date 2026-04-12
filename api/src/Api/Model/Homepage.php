<?php

namespace App\Api\Model;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\HomepageProvider;
use App\Entity\Game\Game;
use App\Entity\Role;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/homepage',
            name: 'api_homepage',
            provider: HomepageProvider::class,
            normalizationContext: [
                'groups' => 'homepage:read',
            ],
        ),
    ],
)]
class Homepage
{
    /**
     * @param Role[] $lastRoles
     * @param Game[] $lastPublicGames
     */
    public function __construct(
        public array $lastRoles = [],
        public array $lastPublicGames = [],
    ) {
    }
}
