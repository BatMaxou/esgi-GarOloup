<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\ResetGameMasterEvent;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Fixtures\Story\ClassicGame\ClassicGameConfiguredStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameDispatchedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameLaunchedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameWithGameMasterSettedStory;
use App\Fixtures\Story\ClassicGame\GameMaster\RandomDispatch\ClassicGameWithGameMasterSettedAndRandomDispatchStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class ResetGameMasterTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_host_can_reset_game_master(): void
    {
        $story = ThereIs::aStory(ClassicGameWithGameMasterSettedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameWithGameMasterSettedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameWithGameMasterSettedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $gameMasterPlayerBuilder = $story->get(ClassicGameWithGameMasterSettedStory::GAME_MASTER);
        $this->assertInstanceOf(PlayerBuilder::class, $gameMasterPlayerBuilder);

        $response = When::asUser($userBuilder)->game()->resetGameMaster();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameInitialisationStepEnum::GAME_MASTER_CHOICE, $game->getInitialisationStep());
        $this->assertNull($game->getGameMaster());

        $gameMaster = $gameMasterPlayerBuilder->getEntity();
        $this->assertFalse($gameMaster->isDead());
        $this->assertEquals($game->getId(), $gameMaster->getGame()?->getId());
    }

    public function test_host_can_reset_game_master_with_random_dispatch(): void
    {
        $story = ThereIs::aStory(ClassicGameWithGameMasterSettedAndRandomDispatchStory::class)->execute();
        $userBuilder = $story->get(ClassicGameWithGameMasterSettedAndRandomDispatchStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameWithGameMasterSettedAndRandomDispatchStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $response = When::asUser($userBuilder)->game()->resetGameMaster();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameInitialisationStepEnum::GAME_MASTER_CHOICE, $game->getInitialisationStep());
        $this->assertNull($game->getGameMaster());
    }

    public function test_anonymous_cant_reset_game_master(): void
    {
        When::game()->resetGameMaster();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_player_cant_reset_game_master(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->resetGameMaster();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_temp_user_without_player_cant_reset_game_master(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->game()->resetGameMaster();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_non_host_player_cant_reset_game_master(): void
    {
        $story = ThereIs::aStory(ClassicGameWithGameMasterSettedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameWithGameMasterSettedStory::USER_1);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->resetGameMaster();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_reset_game_master_if_no_game_master(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->resetGameMaster();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_reset_game_master_if_game_not_in_dispatch_step(): void
    {
        $story = ThereIs::aStory(ClassicGameDispatchedWithGameMasterStory::class)->execute();
        $userBuilder = $story->get(ClassicGameDispatchedWithGameMasterStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->resetGameMaster();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_reset_game_master_if_game_already_launched(): void
    {
        $story = ThereIs::aStory(ClassicGameLaunchedWithGameMasterStory::class)->execute();
        $userBuilder = $story->get(ClassicGameLaunchedWithGameMasterStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->resetGameMaster();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_reset_game_master(): void
    {
        $story = ThereIs::aStory(ClassicGameWithGameMasterSettedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameWithGameMasterSettedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameWithGameMasterSettedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($userBuilder)->game()->resetGameMaster();
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(ResetGameMasterEvent::class, $userBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
