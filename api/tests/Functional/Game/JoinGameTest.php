<?php

namespace App\Tests\Functional\Game;

use App\Entity\Event\Game\JoinGameEvent;
use App\Fixtures\Factory\GameFactory;
use App\Fixtures\Factory\PlayerFactory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class JoinGameTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_anonymous_cant_join_game(): void
    {
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->build();

        When::game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_can_join_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->build();

        $response = When::asUser($userBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        $this->assertEquals(2, $gameBuilder->getEntity()->getPlayers()->count());
        $this->assertTrue($response->get('[success]'));
    }

    public function test_temp_user_can_join_game(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->build();

        $response = When::asTempUser($tempUserBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        $this->assertEquals(2, $gameBuilder->getEntity()->getPlayers()->count());
        $this->assertTrue($response->get('[success]'));
    }

    public function test_user_cant_join_game_if_already_have_one(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->build();

        When::asUser($userBuilder)->game()->create();
        $this->assertResponseStatusCodeSame(201);

        When::game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(409);
    }

    public function test_temp_user_cant_join_game_if_already_have_one(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->build();

        When::asTempUser($tempUserBuilder)->game()->create();
        $this->assertResponseStatusCodeSame(201);

        When::game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(409);
    }

    public function test_user_can_join_game_if_all_its_players_are_dead(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $firstGameBuilder = ThereIs::aGame()->build();
        $secondGameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->build();
        ThereIs::aPlayer()->withUser($userBuilder)->withGame($firstGameBuilder)->dead()->build();

        $response = When::asUser($userBuilder)->game()->join($secondGameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        $this->assertEquals(2, $secondGameBuilder->getEntity()->getPlayers()->count());
        $this->assertTrue($response->get('[success]'));
    }

    public function test_temp_user_can_join_game_if_all_its_players_are_dead(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();
        $firstGameBuilder = ThereIs::aGame()->build();
        $secondGameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->build();
        ThereIs::aPlayer()->withTempUser($tempUserBuilder)->withGame($firstGameBuilder)->dead()->build();

        $response = When::asTempUser($tempUserBuilder)->game()->join($secondGameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        $this->assertEquals(2, $secondGameBuilder->getEntity()->getPlayers()->count());
        $this->assertTrue($response->get('[success]'));
    }

    public function test_user_cant_join_game_without_join_code(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        ThereIs::aGame()->build();

        When::asUser($userBuilder)->game()->join(null);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_temp_user_cant_join_game_without_join_code(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();
        ThereIs::aGame()->build();

        When::asTempUser($tempUserBuilder)->game()->join(null);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_user_cant_join_inexistant_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->join('_this_will_never_exist_');
        $this->assertResponseStatusCodeSame(404);

        $this->assertEquals(0, GameFactory::count());
        $this->assertEquals(0, PlayerFactory::count());
    }

    public function test_temp_user_cant_join_inexistant_game(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->game()->join('_this_will_never_exist_');
        $this->assertResponseStatusCodeSame(404);

        $this->assertEquals(0, GameFactory::count());
        $this->assertEquals(0, PlayerFactory::count());
    }

    public function test_user_cant_join_game_if_already_launched(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->closed()->build();

        When::asUser($userBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_temp_user_cant_join_game_if_already_launched(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->closed()->build();

        When::asTempUser($tempUserBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_temp_user_cant_join_game_if_username_already_taken_by_another_user(): void
    {
        $userBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();
        $tempUserBuilder = ThereIs::aTempUser()->withUsername('SLiipMan')->build();
        $hostUserBuilder = ThereIs::anUser()->withUsername('Mister_man_l-ost')->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($hostUserBuilder)->build();
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->withHost($hostBuilder)->build();

        When::asUser($userBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        When::asTempUser($tempUserBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(409);
    }

    public function test_user_joining_renames_conflicting_temp_user(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->withUsername('SLiipMan')->build();
        $userBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();
        $hostUserBuilder = ThereIs::anUser()->withUsername('Mister_man_l-ost')->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($hostUserBuilder)->build();
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->withHost($hostBuilder)->build();

        When::asTempUser($tempUserBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        When::asUser($userBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        $this->assertEquals(3, $gameBuilder->getEntity()->getPlayers()->count());
        $this->assertEquals('SLiipMan-2', $tempUserBuilder->getEntity()->getUsername());
    }

    public function test_user_joining_renames_conflicting_temp_user_with_incremented_counter(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->withUsername('SLiipMan')->build();
        $secondTempUserBuilder = ThereIs::aTempUser()->withUsername('SLiipMan-2')->build();
        $userBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();
        $hostUserBuilder = ThereIs::anUser()->withUsername('Mister_man_l-ost')->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($hostUserBuilder)->build();
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->withHost($hostBuilder)->build();

        When::asTempUser($tempUserBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        When::asTempUser($secondTempUserBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        When::asUser($userBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        $this->assertEquals(4, $gameBuilder->getEntity()->getPlayers()->count());
        $this->assertEquals('SLiipMan-3', $tempUserBuilder->getEntity()->getUsername());
    }

    public function test_game_event_dispatched_when_joining_game(): void
    {
        $userBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();
        $gameBuilder = ThereIs::aGame()->withJoinCode('Do!BeShy')->build();

        $response = When::asUser($userBuilder)->game()->join($gameBuilder->joinCode);
        $this->assertResponseStatusCodeSame(201);

        $this->assertCollected(JoinGameEvent::class, $userBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
