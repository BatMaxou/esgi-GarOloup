<?php

namespace App\Tests\Functionnal\User;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class GetCurrentGameTest extends GarOloupApiTestCase
{
    public function test_anonymous_do_not_have_a_current_game(): void
    {
        When::game()->getCurrent();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_game_do_not_have_a_current_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder);
        When::game()->getCurrent();
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_temp_user_without_game_do_not_have_a_current_game(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder);
        When::game()->getCurrent();
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_user_can_retrieve_it_current_game(): void
    {
        $userBuilder = ThereIs::anUser()->build(5);
        $gameBuilder = ThereIs::aGame()->build(3);
        ThereIs::aPlayer()->withUser($userBuilder)->withGame($gameBuilder)->build();

        When::asUser($userBuilder);
        $response = When::game()->getCurrent();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals($gameBuilder->getEntity()->getId(), $response->get('[id]'));
    }

    public function test_temp_user_can_retrieve_it_current_game(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build(5);
        $gameBuilder = ThereIs::aGame()->build(3);
        ThereIs::aPlayer()->withTempUser($tempUserBuilder)->withGame($gameBuilder)->build();

        When::asTempUser($tempUserBuilder);
        $response = When::game()->getCurrent();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals($gameBuilder->getEntity()->getId(), $response->get('[id]'));
    }

    // TODO: normalization tests
}
