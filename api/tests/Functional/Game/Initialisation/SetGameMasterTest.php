<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\SetGameMasterEvent;
use App\Enum\Game\GameStepEnum;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class SetGameMasterTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_anonymous_cant_set_game_master(): void
    {
        When::game()->setGameMaster('random-guy-id');
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_host_can_set_game_master(): void
    {
        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();

        $targetUserBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::GAME_MASTER_CHOICE)
            ->build()
        ;
        $targetPlayerBuilder = ThereIs::aPlayer()->withUser($targetUserBuilder)->withGame($gameBuilder)->build();

        $response = When::asUser($hostBuilder)->game()->setGameMaster($targetPlayerBuilder->getEntity()->getId());
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $this->assertEquals($targetPlayerBuilder->getEntity()->getId(), $gameBuilder->getEntity()->getGameMaster()?->getId());
    }

    public function test_temp_user_host_can_set_game_master(): void
    {
        $hostBuilder = ThereIs::aTempUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withTempUser($hostBuilder)->build();
        $targetUserBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->withHost($hostPlayerBuilder)->withStep(GameStepEnum::GAME_MASTER_CHOICE)->build();
        $targetPlayerBuilder = ThereIs::aPlayer()->withUser($targetUserBuilder)->withGame($gameBuilder)->build();

        $response = When::asTempUser($hostBuilder)->game()->setGameMaster($targetPlayerBuilder->getEntity()->getId());
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $this->assertEquals($targetPlayerBuilder->getEntity()->getId(), $gameBuilder->getEntity()->getGameMaster()?->getId());
    }

    public function test_user_without_player_cant_set_game_master(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->setGameMaster('random-guy-id');
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_temp_user_without_player_cant_set_game_master(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->game()->setGameMaster('random-guy-id');
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_non_host_player_cant_set_game_master(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->withStep(GameStepEnum::GAME_MASTER_CHOICE)->build();
        ThereIs::aPlayer()->withUser($userBuilder)->withGame($gameBuilder)->build();

        When::asUser($userBuilder)->game()->setGameMaster('random-guy-id');
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_set_game_master_if_game_not_in_game_master_choice_step(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        $targetUserBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->withHost($hostBuilder)->withStep(GameStepEnum::CONFIGURATION)->build();
        $targetPlayerBuilder = ThereIs::aPlayer()->withUser($targetUserBuilder)->withGame($gameBuilder)->build();

        When::asUser($userBuilder)->game()->setGameMaster($targetPlayerBuilder->getEntity()->getId());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_set_unknown_player_as_game_master(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        ThereIs::aGame()->withHost($hostBuilder)->withStep(GameStepEnum::GAME_MASTER_CHOICE)->build();

        When::asUser($userBuilder)->game()->setGameMaster('omg-i-dont-exist');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_host_cant_set_player_from_another_game_as_game_master(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        ThereIs::aGame()->withHost($hostBuilder)->withStep(GameStepEnum::GAME_MASTER_CHOICE)->build();

        $otherPlayerBuilder = ThereIs::aPlayer()->build();
        ThereIs::aGame()->build();

        When::asUser($userBuilder)->game()->setGameMaster($otherPlayerBuilder->getEntity()->getId());
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_game_event_dispatched_by_set_game_master(): void
    {
        $userBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();
        $targetUserBuilder = ThereIs::anUser()->build();
        $gameBuilder = ThereIs::aGame()->withHost($hostBuilder)->withStep(GameStepEnum::GAME_MASTER_CHOICE)->build();
        $targetPlayerBuilder = ThereIs::aPlayer()->withUser($targetUserBuilder)->withGame($gameBuilder)->build();

        When::asUser($userBuilder)->game()->setGameMaster($targetPlayerBuilder->getEntity()->getId());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(SetGameMasterEvent::class, $userBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
