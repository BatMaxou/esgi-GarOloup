<?php

namespace App\Tests\Functional\User;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class MeTest extends GarOloupApiTestCase
{
    public function test_anonymous_do_not_have_information(): void
    {
        When::me()->get();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_can_get_it_own_information(): void
    {
        $userBuilder = ThereIs::anUser()->withUsername('SliipMan')->build();

        When::asUser($userBuilder);
        $response = When::me()->get();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals('User', $response->get('[@type]'));
        $this->assertEquals($userBuilder->username, $response->get('[username]'));
    }

    public function test_temp_user_can_get_it_own_information(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->withUsername('SLiipMan')->build();

        When::asTempUser($tempUserBuilder);
        $response = When::me()->get();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals('TempUser', $response->get('[@type]'));
        $this->assertEquals($tempUserBuilder->username, $response->get('[username]'));
    }
}
