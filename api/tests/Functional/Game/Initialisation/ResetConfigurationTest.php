<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\ResetConfigurationEvent;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Fixtures\Story\ClassicGame\ClassicGameClosedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameConfiguredStory;
use App\Fixtures\Story\ClassicGame\ClassicGameLaunchedStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameConfiguredWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameWithGameMasterSettedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class ResetConfigurationTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_host_can_reset_configuration(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameConfiguredStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $response = When::asUser($userBuilder)->game()->resetConfiguration();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameInitialisationStepEnum::CONFIGURATION, $game->getInitialisationStep());
        $this->assertNull($game->getConfiguration()->getComposition());
        $this->assertFalse($game->getConfiguration()->isWithGameMaster());
        $this->assertTrue($game->getConfiguration()->isWithRandomDispatch());
    }

    public function test_host_can_reset_configuration_from_game_master_choice(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredWithGameMasterStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameConfiguredWithGameMasterStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $response = When::asUser($userBuilder)->game()->resetConfiguration();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameInitialisationStepEnum::CONFIGURATION, $game->getInitialisationStep());
        $this->assertNull($game->getConfiguration()->getComposition());
        $this->assertFalse($game->getConfiguration()->isWithGameMaster());
        $this->assertTrue($game->getConfiguration()->isWithRandomDispatch());
    }

    public function test_anonymous_cant_reset_configuration(): void
    {
        When::game()->resetConfiguration();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_player_cant_reset_configuration(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->resetConfiguration();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_temp_user_without_player_cant_reset_configuration(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->game()->resetConfiguration();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_non_host_player_cant_reset_configuration(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredStory::USER_1);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->resetConfiguration();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_reset_configuration_if_game_not_in_dispatch_step(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->resetConfiguration();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_reset_configuration_with_game_master(): void
    {
        $story = ThereIs::aStory(ClassicGameWithGameMasterSettedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameWithGameMasterSettedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->resetConfiguration();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_reset_configuration_if_game_already_launched(): void
    {
        $story = ThereIs::aStory(ClassicGameLaunchedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameLaunchedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->resetConfiguration();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_reset_configuration(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameConfiguredStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($userBuilder)->game()->resetConfiguration();
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(ResetConfigurationEvent::class, $userBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
