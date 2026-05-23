<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\SetGameConfigurationEvent;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Fixtures\Story\ClassicGame\ClassicGameClosedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\User\TempUserBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class SetGameConfigurationTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_host_can_set_game_configuration(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameClosedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        $response = When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $this->assertEquals(GameInitialisationStepEnum::DISPATCH, $gameBuilder->getEntity()->getInitialisationStep());
        $this->assertNotNull($gameBuilder->getEntity()->getConfiguration()->getComposition());
    }

    public function test_host_can_set_game_configuration_with_game_master(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameClosedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        $response = When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder, true);
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $this->assertEquals(GameInitialisationStepEnum::GAME_MASTER_CHOICE, $gameBuilder->getEntity()->getInitialisationStep());
        $this->assertNotNull($gameBuilder->getEntity()->getConfiguration()->getComposition());
    }

    public function test_cant_set_game_configuration_for_inexistant_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_anonymous_cant_set_game_configuration(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_cant_set_game_configuration(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $tempUserBuilder = $story->get(ClassicGameClosedStory::TEMP_USER_1);
        $this->assertInstanceOf(TempUserBuilder::class, $tempUserBuilder);

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asTempUser($tempUserBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_random_player_cant_set_game_configuration(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_set_game_configuration_without_configuration(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        $compositionBuilder = ThereIs::aComposition();

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_with_unmatching_composition_roles_number(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 2)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_with_negative_role_number(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), -4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_with_invalid_max_per_game(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        $roleBuiler = ThereIs::aRole()->villager()->withMaxPerGame(2)->build();
        $compositionBuilder = ThereIs::aComposition()->withRole($roleBuiler, 6);

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_with_invalid_min_players(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        $roleBuiler = ThereIs::aRole()->villager()->withMinPlayers(8)->build();
        $compositionBuilder = ThereIs::aComposition()->withRole($roleBuiler, 6);

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_with_role_given_more_than_once(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 3)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
            ->withRole($roleBagBuilder->getVillager(), 1)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_if_game_has_already_pass_configuration_step(): void
    {
        $storyBuilder = ThereIs::aStory(ClassicGameClosedStory::class);
        $story = $storyBuilder->getEntity();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameClosedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $gameBuilder->withInitialisationStep(GameInitialisationStepEnum::GAME_MASTER_CHOICE);
        $storyBuilder->execute();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_configuration_with_game_master_and_random_dispatch_does_not_triggers_random_game_role_dispatch(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameClosedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder, true);
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameInitialisationStepEnum::GAME_MASTER_CHOICE, $game->getInitialisationStep());
        foreach ($game->getPlayers() as $player) {
            $this->assertNull($player->getRole());
        }
    }

    public function test_configuration_with_game_master_and_without_random_dispatch_does_not_trigger_random_game_role_dispatch(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameClosedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder, true, false);
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameInitialisationStepEnum::GAME_MASTER_CHOICE, $game->getInitialisationStep());
        foreach ($game->getPlayers() as $player) {
            $this->assertNull($player->getRole());
        }
    }

    public function test_game_event_dispatched_by_set_game_configuration(): void
    {
        $story = ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        $userBuilder = $story->get(ClassicGameClosedStory::HOST);
        $this->assertInstanceOf(UserBuilder::class, $userBuilder);
        $gameBuilder = $story->get(ClassicGameClosedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        $response = When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(SetGameConfigurationEvent::class, $userBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
