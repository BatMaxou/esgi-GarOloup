<?php

namespace App\Tests\Functional\User;

use App\Fixtures\Factory\User\TempUserFactory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class CreateTempUserTest extends GarOloupApiTestCase
{
    public function test_can_get_temp_user(): void
    {
        When::tempUser()->get('SLiipMan');
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals(1, TempUserFactory::count());
    }

    public function test_can_get_temp_user_tokens(): void
    {
        $response = When::tempUser()->get('SLiipMan');
        $this->assertResponseStatusCodeSame(200);

        $this->assertNotEmpty($response->get('[token]'));
        $this->assertNotEmpty($response->get('[refreshToken]'));
    }

    public function test_can_get_multiple_temp_user_with_same_ip(): void
    {
        When::tempUser()->get('SLiipMan');
        $this->assertResponseStatusCodeSame(200);

        When::tempUser()->get('SLiipGirl');
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals(2, TempUserFactory::count());
    }

    public function test_cant_get_multiple_temp_user_with_same_username_and_ip(): void
    {
        When::tempUser()->get('SLiipMan');
        $this->assertResponseStatusCodeSame(200);

        When::tempUser()->get('SLiipMan');
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals(1, TempUserFactory::count());
    }

    public function test_cant_get_temp_user_if_already_have_one(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->withUsername('SLiipMan')->build();

        When::asTempUser($tempUserBuilder);
        $response = When::tempUser()->get('SlaaapMan');
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals(1, TempUserFactory::count());
        $this->assertNotEmpty($response->get('[token]'));
        $this->assertNotEmpty($response->get('[refreshToken]'));
    }

    public function test_cant_get_temp_user_if_already_have_classic_user(): void
    {
        $userBuilder = ThereIs::anUser()->withEmail('sliipman@garoloup.fr')->withPassword('slaap')->build();

        When::asUser($userBuilder);
        When::tempUser()->get('SLiipMan');
        $this->assertResponseStatusCodeSame(409);

        $this->assertEquals(0, TempUserFactory::count());
    }
}
