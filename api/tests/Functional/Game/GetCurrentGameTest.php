<?php

namespace App\Tests\Functional\Game;

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
        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->build();
        ThereIs::aPlayer()->withUser($userBuilder)->withGame($gameBuilder)->build();

        $response = When::asUser($userBuilder)->game()->getCurrent();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals($gameBuilder->getEntity()->getId(), $response->get('[id]'));
    }

    public function test_temp_user_can_retrieve_it_current_game(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();
        $gameBuilder = ThereIs::aGame()->build();
        ThereIs::aPlayer()->withTempUser($tempUserBuilder)->withGame($gameBuilder)->build();

        $response = When::asTempUser($tempUserBuilder)->game()->getCurrent();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals($gameBuilder->getEntity()->getId(), $response->get('[id]'));
    }

    public function test_game_master_can_retrieve_it_current_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $gameMasterBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        $gameBuilder = ThereIs::aGame()->withGameMaster($gameMasterBuilder)->build();

        $response = When::asUser($userBuilder)->game()->getCurrent();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals($gameBuilder->getEntity()->getId(), $response->get('[id]'));
    }
}
