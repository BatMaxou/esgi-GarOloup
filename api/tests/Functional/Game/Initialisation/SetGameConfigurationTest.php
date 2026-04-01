<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\SetGameConfigurationEvent;
use App\Enum\Game\GameStepEnum;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class SetGameConfigurationTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_host_can_set_game_configuration(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        $response = When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $this->assertEquals(GameStepEnum::READY, $gameBuilder->getEntity()->getStep());
        $this->assertNotNull($gameBuilder->getEntity()->getConfiguration()->getComposition());
    }

    public function test_host_can_set_game_configuration_with_game_master(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        $response = When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder, true);
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));
        $this->assertEquals(GameStepEnum::GAME_MASTER_CHOICE, $gameBuilder->getEntity()->getStep());
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
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_cant_set_game_configuration(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_random_player_cant_set_game_configuration(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $playerBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withPlayer($playerBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(4, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_host_cant_set_game_configuration_without_configuration(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        ThereIs::aRoleBag()->buildAll();
        ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition();

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_with_unmatching_composition_roles_number(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 2)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_with_negative_role_number(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), -4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_with_invalid_max_per_game(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBuiler = ThereIs::aRole()->villager()->withMaxPerGame(2)->build();

        ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()->withRole($roleBuiler, 6);

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_with_invalid_min_players(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBuiler = ThereIs::aRole()->villager()->withMinPlayers(8)->build();

        ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()->withRole($roleBuiler, 6);

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_with_role_given_more_than_once(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(7, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
            ->withRole($roleBagBuilder->getVillager(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_host_cant_set_game_configuration_if_game_has_already_pass_configuration_step(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(7, true))
            ->withStep(GameStepEnum::GAME_MASTER_CHOICE)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_configuration_without_game_master_and_random_dispatch_triggers_random_game_role_dispatch(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder);
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameStepEnum::READY, $game->getStep());
        foreach ($game->getPlayers() as $player) {
            $this->assertNotNull($player->getRole());
        }
    }

    public function test_configuration_with_game_master_and_random_dispatch_does_not_triggers_random_game_role_dispatch(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder, true);
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameStepEnum::GAME_MASTER_CHOICE, $game->getStep());
        foreach ($game->getPlayers() as $player) {
            $this->assertNull($player->getRole());
        }
    }

    public function test_configuration_with_game_master_and_without_random_dispatch_does_not_trigger_random_game_role_dispatch(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

        $compositionBuilder = ThereIs::aComposition()
            ->withRole($roleBagBuilder->getVillager(), 4)
            ->withRole($roleBagBuilder->getWerewolf(), 2)
        ;

        When::asUser($userBuilder)->game()->setConfiguration($compositionBuilder, true, false);
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameStepEnum::GAME_MASTER_CHOICE, $game->getStep());
        foreach ($game->getPlayers() as $player) {
            $this->assertNull($player->getRole());
        }
    }

    public function test_game_event_dispatched_by_set_game_configuration(): void
    {
        $userBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();
        $hostBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withStep(GameStepEnum::CONFIGURATION)
            ->build();

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
