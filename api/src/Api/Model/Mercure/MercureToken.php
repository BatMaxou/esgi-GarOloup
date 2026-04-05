<?php

namespace App\Api\Model\Mercure;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Api\Provider\Mercure\MercureTokenProvider;

#[ApiResource(
    operations: [
        new Get(
            name: 'api_mercure_token',
            uriTemplate: '/mercure/token',
            provider: MercureTokenProvider::class,
        )
    ],
)]
class MercureToken
{
    public function __construct(
        public ?string $token = null,
    ) {
    }
}
