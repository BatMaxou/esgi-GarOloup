<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\ResetRoleDispatchEvent;
use App\Entity\Game\Role\GameRole;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\ClassicGameDispatchedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\ClassicGameLaunchedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\ClassicGameWithGameMasterSettedStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\RandomDispatch\ClassicGameDispatchedWithGameMasterAndRandomDispatchStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class ResetRoleDispatchTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_game_master_can_reset_role_dispatch(): void
    {
        $story = ThereIs::aStory(ClassicGameDispatchedWithGameMasterStory::class)->execute();
        $gameMasterPlayerBuilder = $story->get(ClassicGameDispatchedWithGameMasterStory::GAME_MASTER);
        $this->assertInstanceOf(PlayerBuilder::class, $gameMasterPlayerBuilder);
        $gameMasterUserBuilder = $gameMasterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $gameMasterUserBuilder);
        $gameBuilder = $story->get(ClassicGameDispatchedWithGameMasterStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        foreach ($gameBuilder->getEntity()->getPlayers() as $player) {
            $this->assertInstanceOf(GameRole::class, $player->getRole());
        }

        $response = When::asUser($gameMasterUserBuilder)->game()->resetRoleDispatch();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameInitialisationStepEnum::DISPATCH, $game->getInitialisationStep());
        foreach ($game->getPlayers() as $player) {
            $this->assertNull($player->getRole());
        }
    }

    public function test_anonymous_cant_reset_role_dispatch(): void
    {
        When::game()->resetRoleDispatch();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_player_cant_reset_role_dispatch(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->resetRoleDispatch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_temp_user_without_player_cant_reset_role_dispatch(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();

        When::asTempUser($tempUserBuilder)->game()->resetRoleDispatch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_non_game_master_player_cant_reset_role_dispatch(): void
    {
        $story = ThereIs::aStory(ClassicGameDispatchedWithGameMasterStory::class)->execute();
        $playerBuilder = $story->get(ClassicGameDispatchedWithGameMasterStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $playerBuilder);
        $userBuilder = $playerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        When::asUser($userBuilder)->game()->resetRoleDispatch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_master_cant_reset_role_dispatch_if_game_not_in_finish_step(): void
    {
        $story = ThereIs::aStory(ClassicGameWithGameMasterSettedStory::class)->execute();
        $gameMasterPlayerBuilder = $story->get(ClassicGameWithGameMasterSettedStory::GAME_MASTER);
        $this->assertInstanceOf(PlayerBuilder::class, $gameMasterPlayerBuilder);
        $gameMasterUserBuilder = $gameMasterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $gameMasterUserBuilder);

        When::asUser($gameMasterUserBuilder)->game()->resetRoleDispatch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_master_cant_reset_role_dispatch_if_game_already_launched(): void
    {
        $story = ThereIs::aStory(ClassicGameLaunchedWithGameMasterStory::class)->execute();
        $gameMasterPlayerBuilder = $story->get(ClassicGameLaunchedWithGameMasterStory::GAME_MASTER);
        $this->assertInstanceOf(PlayerBuilder::class, $gameMasterPlayerBuilder);
        $gameMasterUserBuilder = $gameMasterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $gameMasterUserBuilder);

        When::asUser($gameMasterUserBuilder)->game()->resetRoleDispatch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_master_cant_reset_role_dispatch_with_random_dispatch(): void
    {
        $story = ThereIs::aStory(ClassicGameDispatchedWithGameMasterAndRandomDispatchStory::class)->execute();
        $gameMasterPlayerBuilder = $story->get(ClassicGameDispatchedWithGameMasterAndRandomDispatchStory::GAME_MASTER);
        $this->assertInstanceOf(PlayerBuilder::class, $gameMasterPlayerBuilder);
        $gameMasterUserBuilder = $gameMasterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $gameMasterUserBuilder);

        When::asUser($gameMasterUserBuilder)->game()->resetRoleDispatch();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_reset_role_dispatch(): void
    {
        $story = ThereIs::aStory(ClassicGameDispatchedWithGameMasterStory::class)->execute();
        $gameMasterPlayerBuilder = $story->get(ClassicGameDispatchedWithGameMasterStory::GAME_MASTER);
        $this->assertInstanceOf(PlayerBuilder::class, $gameMasterPlayerBuilder);
        $gameMasterUserBuilder = $gameMasterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $gameMasterUserBuilder);
        $gameBuilder = $story->get(ClassicGameDispatchedWithGameMasterStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($gameMasterUserBuilder)->game()->resetRoleDispatch();
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(ResetRoleDispatchEvent::class, $gameMasterUserBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
