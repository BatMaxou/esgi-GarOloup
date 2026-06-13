<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\LeaveGameEvent;
use App\Fixtures\Factory\Game\GameFactory;
use App\Fixtures\Factory\Game\PlayerFactory;
use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameClosedStory;
use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameFilledStory;
use App\Fixtures\Story\Game\GameCreatedStory;
use App\Repository\Game\GameRepository;
use App\Repository\Game\PlayerRepository;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class LeaveGameTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_anonymous_cant_leave_game(): void
    {
        When::player()->leave();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_player_cant_leave_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->player()->leave();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_player_can_leave_open_game(): void
    {
        $story = ThereIs::aStory(ClassicGameFilledStory::class)->execute();
        $userBuilder = $story->get(ClassicGameFilledStory::USER_1);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        $initialCount = PlayerFactory::count();

        When::asUser($userBuilder)->player()->leave();
        $this->assertResponseStatusCodeSame(204);

        $this->assertEquals($initialCount - 1, PlayerFactory::count());
        $this->assertCount(0, $this->getService(PlayerRepository::class)->findByUser($userBuilder->getEntity()));
    }

    public function test_temp_user_can_leave_open_game(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();
        $gameBuilder = ThereIs::aGame()->build();
        ThereIs::aPlayer()->withTempUser($tempUserBuilder)->withGame($gameBuilder)->build();

        $this->assertEquals(2, PlayerFactory::count());

        When::asTempUser($tempUserBuilder)->player()->leave();
        $this->assertResponseStatusCodeSame(204);

        $this->assertEquals(1, PlayerFactory::count());
    }

    public function test_host_leaving_open_game_hands_host_to_another_player(): void
    {
        $story = ThereIs::aStory(ClassicGameFilledStory::class)->execute();
        $hostUserBuilder = $story->get(ClassicGameFilledStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $hostUserBuilder);
        $hostPlayerBuilder = $story->get(ClassicGameFilledStory::HOST_PLAYER);
        $this->assertInstanceOf(PlayerBuilder::class, $hostPlayerBuilder);
        $gameBuilder = $story->get(ClassicGameFilledStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $oldHostPlayerId = $hostPlayerBuilder->getEntity()->getId()?->toString();
        $gameId = $gameBuilder->getEntity()->getId();
        $initialCount = PlayerFactory::count();

        When::asUser($hostUserBuilder)->player()->leave();
        $this->assertResponseStatusCodeSame(204);

        $this->assertEquals($initialCount - 1, PlayerFactory::count());

        $this->getEntityManager()->clear();
        $game = $this->getService(GameRepository::class)->find($gameId);
        $this->assertNotNull($game);
        $this->assertCount($initialCount - 1, $game->getPlayers());
        $this->assertNotEquals($oldHostPlayerId, $game->getHost()->getId()?->toString());
    }

    public function test_last_player_leaving_open_game_deletes_the_game(): void
    {
        $story = ThereIs::aStory(GameCreatedStory::class)->execute();
        $hostUserBuilder = $story->get(GameCreatedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $hostUserBuilder);

        $this->assertEquals(1, GameFactory::count());
        $this->assertEquals(1, PlayerFactory::count());

        When::asUser($hostUserBuilder)->player()->leave();
        $this->assertResponseStatusCodeSame(204);

        $this->assertEquals(0, GameFactory::count());
        $this->assertEquals(0, PlayerFactory::count());
    }

    public function test_player_cant_leave_closed_game(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::USER_1);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->player()->leave();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_when_leaving_game(): void
    {
        $story = ThereIs::aStory(ClassicGameFilledStory::class)->execute();
        $userBuilder = $story->get(ClassicGameFilledStory::USER_1);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameFilledStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $gameId = $gameBuilder->getEntity()->getId();

        When::asUser($userBuilder)->player()->leave();
        $this->assertResponseStatusCodeSame(204);

        $this->assertCollected(LeaveGameEvent::class, $userBuilder->username, $gameId);
        $this->assertEventCollectedNumber(1);
    }
}
