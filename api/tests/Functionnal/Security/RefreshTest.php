<?php

namespace App\Tests\Functionnal\Security;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class RefreshTest extends GarOloupApiTestCase
{
    public function test_user_can_refresh_token(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $refreshTokenBuilder = ThereIs::aRefreshToken()->withUsername($userBuilder->getEntity()->getUserIdentifier())->withToken('_refresh_')->build();

        $response = When::auth()->refresh($refreshTokenBuilder->token);
        $this->assertResponseStatusCodeSame(200);

        $this->assertNotEmpty($response->get('[token]'));
        $this->assertNotEmpty($response->get('[refresh_token]'));
    }

    public function test_user_cant_refresh_token_with_invalid_token(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        ThereIs::aRefreshToken()->withUsername($userBuilder->getEntity()->getUserIdentifier())->withToken('_refresh_')->build();

        When::auth()->refresh('invalid');
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_cant_refresh_inexistant_token(): void
    {
        When::auth()->refresh('_refresh_');
        $this->assertResponseStatusCodeSame(401);
    }
}
