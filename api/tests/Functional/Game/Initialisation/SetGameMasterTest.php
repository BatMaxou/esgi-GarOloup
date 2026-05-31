<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\SetGameMasterEvent;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Fixtures\Story\ClassicGame\ClassicGameClosedStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameConfiguredWithGameMasterStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
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
        $story = ThereIs::aStory(ClassicGameConfiguredWithGameMasterStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $targetPlayerBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::PLAYER_7);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);

        $response = When::asUser($userBuilder)->game()->setGameMaster($targetPlayerBuilder->getEntity()->getId());
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $this->assertEquals($targetPlayerBuilder->getEntity()->getId(), $gameBuilder->getEntity()->getGameMaster()?->getId());
        $this->assertEquals(GameInitialisationStepEnum::DISPATCH, $gameBuilder->getEntity()->getInitialisationStep());
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
        $story = ThereIs::aStory(ClassicGameConfiguredWithGameMasterStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::USER_1);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->setGameMaster('random-guy-id');
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_set_game_master_if_game_not_in_game_master_choice_step(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameClosedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $targetPlayerBuilder = $story->get(ClassicGameClosedStory::PLAYER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);

        When::asUser($userBuilder)->game()->setGameMaster($targetPlayerBuilder->getEntity()->getId());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_set_unknown_player_as_game_master(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredWithGameMasterStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->setGameMaster('omg-i-dont-exist');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_host_cant_set_player_from_another_game_as_game_master(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredWithGameMasterStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        $otherStory = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $otherPlayerBuilder = $otherStory->get(ClassicGameClosedStory::PLAYER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $otherPlayerBuilder);

        When::asUser($userBuilder)->game()->setGameMaster($otherPlayerBuilder->getEntity()->getId());
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_game_event_dispatched_by_set_game_master(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredWithGameMasterStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $targetPlayerBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::PLAYER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);

        When::asUser($userBuilder)->game()->setGameMaster($targetPlayerBuilder->getEntity()->getId());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(SetGameMasterEvent::class, $userBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
