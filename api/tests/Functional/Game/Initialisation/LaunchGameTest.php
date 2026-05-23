<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\LaunchGameEvent;
use App\Entity\Game\Role\GameRole;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ClassicGame\ClassicGameClosedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameConfiguredStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\User\TempUserBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class LaunchGameTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_host_can_launch_game(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameConfiguredStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $response = When::asUser($userBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameInitialisationStepEnum::FINISH, $game->getInitialisationStep());
        $this->assertEquals(GameRuntimeStepEnum::SETUP, $game->getRuntimeStep());
    }

    public function test_role_dispatch_when_host_launch_game_without_game_master(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameConfiguredStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        foreach ($gameBuilder->getEntity()->getPlayers() as $player) {
            $this->assertNull($player->getRole());
        }

        $response = When::asUser($userBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        foreach ($gameBuilder->getEntity()->getPlayers() as $player) {
            $this->assertInstanceOf(GameRole::class, $player->getRole());
        }
    }

    public function test_anonymous_cant_launch_game(): void
    {
        When::game()->launch();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_player_cant_launch_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_temp_user_without_player_cant_launch_game(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_random_player_cant_launch_game(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        $tempUserBuilder = $story->get(ClassicGameConfiguredStory::TEMP_USER_1);
        $this->assertInstanceOf(TempUserBuilder::class, $tempUserBuilder);

        When::asTempUser($tempUserBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_launch_game_if_it_is_not_ready(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_launch_game(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        $userBuilder = $story->get(ClassicGameConfiguredStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameConfiguredStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($userBuilder)->game()->launch();
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(LaunchGameEvent::class, $userBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
