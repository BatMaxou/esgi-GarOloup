<?php

namespace App\Tests\Functional\Security;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class LoginTest extends GarOloupApiTestCase
{
    public function test_user_can_retrieve_api_tokens(): void
    {
        $userBuilder = ThereIs::anUser()->withEmail('sliipman@garoloup.fr')->withPassword('slaap')->build();

        $response = When::auth()->login($userBuilder->email, $userBuilder->password);
        $this->assertResponseStatusCodeSame(200);

        $this->assertNotEmpty($response->get('[token]'));
        $this->assertNotEmpty($response->get('[refresh_token]'));
    }

    public function test_user_cant_login_with_invalid_email(): void
    {
        When::auth()->login('invalid@garoloup.fr', 'password');
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_cant_login_with_invalid_password(): void
    {
        $userBuilder = ThereIs::anUser()->withEmail('sliipman@garoloup.fr')->withPassword('slaap')->build();

        When::auth()->login($userBuilder->email, 'invalid');
        $this->assertResponseStatusCodeSame(401);
    }
}
