<?php

namespace App\Tests\Functionnal\Security;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class LoginTest extends GarOloupApiTestCase
{
    public function testUserCanRetrieveApiTokens(): void
    {
        ThereIs::anUser()->withEmail('test@garoloup.fr')->withPassword('password')->build();

        $response = When::auth()->login('test@garoloup.fr', 'password');
        $data = $response->toArray();

        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayHasKey('token', $data);
        $this->assertNotEmpty($data['token']);
        $this->assertArrayHasKey('refresh_token', $data);
        $this->assertNotEmpty($data['refresh_token']);
    }

    public function testUserCantLoginWithInvalidEmail(): void
    {
        When::auth()->login('invalid@garoloup.fr', 'password');

        $this->assertResponseStatusCodeSame(401);
    }

    public function testUserCantLoginWithInvalidPassword(): void
    {
        ThereIs::anUser()->withEmail('test@garoloup.fr')->withPassword('password')->build();

        When::auth()->login('test@garoloup.fr', 'invalid');

        $this->assertResponseStatusCodeSame(401);
    }
}
