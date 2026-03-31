<?php

namespace App\Tests\Functional\Player;

use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class GetCurrentPlayerTest extends GarOloupApiTestCase
{
    public function test_anonymous_do_not_have_a_current_player(): void
    {
        When::player()->getCurrent();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_player_do_not_have_a_current_player(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->player()->getCurrent();
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_temp_user_without_player_do_not_have_a_current_player(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->player()->getCurrent();
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_user_can_retrieve_it_own_current_player(): void
    {
        $userBuilder = ThereIs::anUser()->build(5);
        $gameBuilder = ThereIs::aGame()->build(3);
        $playerBuilder = ThereIs::aPlayer()->withUser($userBuilder)->withGame($gameBuilder)->build();

        $response = When::asUser($userBuilder)->player()->getCurrent();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals($playerBuilder->getEntity()->getUser()?->getId(), $response->get('[user][id]'));
        $this->assertEquals($playerBuilder->getEntity()->getGame()?->getId(), $response->get('[game][id]'));
    }

    public function test_temp_user_can_retrieve_it_own_current_player(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build(5);
        $gameBuilder = ThereIs::aGame()->build(3);
        $playerBuilder = ThereIs::aPlayer()->withTempUser($tempUserBuilder)->withGame($gameBuilder)->build();

        $response = When::asTempUser($tempUserBuilder)->player()->getCurrent();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals($playerBuilder->getEntity()->getTempUser()?->getId(), $response->get('[tempUser][id]'));
        $this->assertEquals($playerBuilder->getEntity()->getGame()?->getId(), $response->get('[game][id]'));
    }

    public function test_user_retrieve_player_of_the_most_recent_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $firstGameBuilder = ThereIs::aGame()->build();
        $secondGameBuilder = ThereIs::aGame()->build();

        $firstPlayerBuilder = ThereIs::aPlayer()
            ->withUser($userBuilder)
            ->withGame($firstGameBuilder)
            ->createdAt(new \DateTimeImmutable('2026-01-01 00:05:00'))
            ->build()
        ;
        ThereIs::aPlayer()
            ->withUser($userBuilder)
            ->withGame($secondGameBuilder)
            ->createdAt(new \DateTimeImmutable('2026-01-01 00:00:00'))
            ->build()
        ;

        $response = When::asUser($userBuilder)->player()->getCurrent();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals($firstPlayerBuilder->getEntity()->getId(), $response->get('[id]'));
    }

    public function test_temp_user_retrieve_player_of_the_most_recent_game(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();
        $firstGameBuilder = ThereIs::aGame()->build();
        $secondGameBuilder = ThereIs::aGame()->build();

        $firstPlayerBuilder = ThereIs::aPlayer()
            ->withTempUser($tempUserBuilder)
            ->withGame($firstGameBuilder)
            ->createdAt(new \DateTimeImmutable('2026-01-01 00:05:00'))
            ->build()
        ;
        ThereIs::aPlayer()
            ->withTempUser($tempUserBuilder)
            ->withGame($secondGameBuilder)
            ->createdAt(new \DateTimeImmutable('2026-01-01 00:00:00'))
            ->build()
        ;

        $response = When::asTempUser($tempUserBuilder)->player()->getCurrent();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals($firstPlayerBuilder->getEntity()->getId(), $response->get('[id]'));
    }

    public function test_user_retrieve_only_player_of_not_finished_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->finished()->build();
        ThereIs::aPlayer()->withUser($userBuilder)->withGame($gameBuilder)->build();

        When::asUser($userBuilder);
        When::player()->getCurrent();
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_temp_user_retrieve_only_player_of_not_finished_game(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();
        $gameBuilder = ThereIs::aGame()->finished()->build();
        ThereIs::aPlayer()->withTempUser($tempUserBuilder)->withGame($gameBuilder)->build();

        When::asTempUser($tempUserBuilder);
        When::player()->getCurrent();
        $this->assertResponseStatusCodeSame(404);
    }
}
