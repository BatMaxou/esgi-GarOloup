<?php

namespace App\Tests\Functional\User;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class ForgotPasswordTest extends GarOloupApiTestCase
{
    public function test_user_can_request_forgot_password_email(): void
    {
        $userBuilder = ThereIs::anUser()->withEmail('sliipman@garoloup.fr')->build();

        $response = When::user()->forgotPassword($userBuilder->email);
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[treated]'));
        $this->assertEmailCount(1);
    }

    public function test_random_request_does_not_send_email(): void
    {
        $response = When::user()->forgotPassword('random@email.com');
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[treated]'));
        $this->assertEmailCount(0);
    }
}
