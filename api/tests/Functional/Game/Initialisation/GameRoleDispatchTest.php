<?php

namespace App\Tests\Functional\Game\Initialisation;

use App\Entity\Event\Game\GameRoleDispatchEvent;
use App\Enum\Game\GameInitialisationStepEnum;
use App\Enum\Game\GameRoleEnum;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class GameRoleDispatchTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_game_master_can_dispatch_roles(): void
    {
        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $playerBuilders = ThereIs::aPlayer()->build(6, true);
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withGameMaster($hostPlayerBuilder)
            ->withPlayers($playerBuilders)
            ->withInitialisationStep(GameInitialisationStepEnum::DISPATCH)
            ->build()
        ;
        ThereIs::aConfiguration()
            ->forGame($gameBuilder)
            ->withGameMaster()
            ->withoutRandomDispatch()
            ->withComposition(
                ThereIs::aComposition()
                    ->withRole($roleBagBuilder->getVillager(), 4)
                    ->withRole($roleBagBuilder->getWerewolf(), 2)
                    ->build()
            )
            ->build()
        ;

        $dispatch = ThereIs::aDispatch()
            ->withEntry($playerBuilders[0], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[1], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[2], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[3], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[4], $roleBagBuilder->getWerewolf())
            ->withEntry($playerBuilders[5], $roleBagBuilder->getWerewolf())
        ;

        $response = When::asUser($hostBuilder)->game()->dispatchRoles($dispatch);
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($response->get('[success]'));

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameInitialisationStepEnum::FINISH, $game->getInitialisationStep());
        $this->assertEquals(GameRoleEnum::VILLAGER, $playerBuilders[0]->getEntity()->getRole()?->getType());
        $this->assertEquals(GameRoleEnum::VILLAGER, $playerBuilders[1]->getEntity()->getRole()?->getType());
        $this->assertEquals(GameRoleEnum::VILLAGER, $playerBuilders[2]->getEntity()->getRole()?->getType());
        $this->assertEquals(GameRoleEnum::VILLAGER, $playerBuilders[3]->getEntity()->getRole()?->getType());
        $this->assertEquals(GameRoleEnum::WEREWOLF, $playerBuilders[4]->getEntity()->getRole()?->getType());
        $this->assertEquals(GameRoleEnum::WEREWOLF, $playerBuilders[5]->getEntity()->getRole()?->getType());
    }

    public function test_anonymous_cant_dispatch_roles(): void
    {
        $dispatch = ThereIs::aDispatch();

        When::game()->dispatchRoles($dispatch);
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_player_cant_dispatch_roles(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $dispatch = ThereIs::aDispatch();

        When::asUser($userBuilder)->game()->dispatchRoles($dispatch);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_temp_user_without_player_cant_dispatch_roles(): void
    {
        $tempUserBuilder = ThereIs::aTempUser()->build();
        $dispatch = ThereIs::aDispatch();

        When::asTempUser($tempUserBuilder)->game()->dispatchRoles($dispatch);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_non_game_master_player_cant_dispatch_roles(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $playerBuilder = ThereIs::aPlayer()->withUser($userBuilder)->build();

        $playerBuilders = [$playerBuilder, ...ThereIs::aPlayer()->build(5, true)];

        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withGameMaster($hostPlayerBuilder)
            ->withPlayer($playerBuilder)
            ->withPlayers(ThereIs::aPlayer()->build(5, true))
            ->withInitialisationStep(GameInitialisationStepEnum::DISPATCH)
            ->build()
        ;
        ThereIs::aConfiguration()
            ->forGame($gameBuilder)
            ->withGameMaster()
            ->withoutRandomDispatch()
            ->withComposition(
                ThereIs::aComposition()
                    ->withRole($roleBagBuilder->getVillager(), 4)
                    ->withRole($roleBagBuilder->getWerewolf(), 2)
                    ->build()
            )
            ->build()
        ;

        $dispatch = ThereIs::aDispatch()
            ->withEntry($playerBuilders[0], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[1], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[2], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[3], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[4], $roleBagBuilder->getWerewolf())
            ->withEntry($playerBuilders[5], $roleBagBuilder->getWerewolf())
        ;

        When::asUser($userBuilder)->game()->dispatchRoles($dispatch);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_master_cant_dispatch_roles_if_game_not_in_dispatch_step(): void
    {
        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();

        $playerBuilders = ThereIs::aPlayer()->build(6, true);

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withGameMaster($hostPlayerBuilder)
            ->withPlayers($playerBuilders)
            ->withInitialisationStep(GameInitialisationStepEnum::FINISH)
            ->build()
        ;
        ThereIs::aConfiguration()
            ->forGame($gameBuilder)
            ->withGameMaster()
            ->withoutRandomDispatch()
            ->withComposition(
                ThereIs::aComposition()
                    ->withRole($roleBagBuilder->getVillager(), 4)
                    ->withRole($roleBagBuilder->getWerewolf(), 2)
                    ->build()
            )
            ->build()
        ;

        $dispatch = ThereIs::aDispatch()
            ->withEntry($playerBuilders[0], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[1], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[2], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[3], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[4], $roleBagBuilder->getWerewolf())
            ->withEntry($playerBuilders[5], $roleBagBuilder->getWerewolf())
        ;

        When::asUser($hostBuilder)->game()->dispatchRoles($dispatch);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_master_cant_dispatch_roles_with_mismatching_player_count(): void
    {
        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $playerBuilders = ThereIs::aPlayer()->build(6, true);
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withGameMaster($hostPlayerBuilder)
            ->withPlayers($playerBuilders)
            ->withInitialisationStep(GameInitialisationStepEnum::DISPATCH)
            ->build()
        ;
        ThereIs::aConfiguration()
            ->forGame($gameBuilder)
            ->withGameMaster()
            ->withoutRandomDispatch()
            ->withComposition(
                ThereIs::aComposition()
                    ->withRole($roleBagBuilder->getVillager(), 4)
                    ->withRole($roleBagBuilder->getWerewolf(), 2)
                    ->build()
            )
            ->build()
        ;

        $dispatch = ThereIs::aDispatch()
            ->withEntry($playerBuilders[0], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[1], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[2], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[3], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[4], $roleBagBuilder->getWerewolf())
        ;

        When::asUser($hostBuilder)->game()->dispatchRoles($dispatch);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_game_master_cant_dispatch_roles_with_unknown_player(): void
    {
        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();

        $targetTempUserBuilder = ThereIs::aTempUser()->build();
        $targetPlayerBuilder = ThereIs::aPlayer()->withTempUser($targetTempUserBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $playerBuilders = ThereIs::aPlayer()->build(6, true);
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withGameMaster($hostPlayerBuilder)
            ->withPlayers($playerBuilders)
            ->withInitialisationStep(GameInitialisationStepEnum::DISPATCH)
            ->build()
        ;
        ThereIs::aConfiguration()
            ->forGame($gameBuilder)
            ->withGameMaster()
            ->withoutRandomDispatch()
            ->withComposition(
                ThereIs::aComposition()
                    ->withRole($roleBagBuilder->getVillager(), 4)
                    ->withRole($roleBagBuilder->getWerewolf(), 2)
                    ->build()
            )
            ->build()
        ;

        $dispatch = ThereIs::aDispatch()
            ->withEntry($targetPlayerBuilder, $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[1], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[2], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[3], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[4], $roleBagBuilder->getWerewolf())
            ->withEntry($playerBuilders[5], $roleBagBuilder->getWerewolf())
        ;

        When::asUser($hostBuilder)->game()->dispatchRoles($dispatch);
        $this->assertResponseStatusCodeSame(400);
    }

    public function test_non_game_master_game_can_not_dispatch_roles(): void
    {
        $hostBuilder = ThereIs::anUser()->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $playerBuilders = [$hostPlayerBuilder, ...ThereIs::aPlayer()->build(5, true)];
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withPlayers($playerBuilders)
            ->withInitialisationStep(GameInitialisationStepEnum::DISPATCH)
            ->build()
        ;
        ThereIs::aConfiguration()
            ->forGame($gameBuilder)
            ->withGameMaster()
            ->withoutRandomDispatch()
            ->withComposition(
                ThereIs::aComposition()
                    ->withRole($roleBagBuilder->getVillager(), 4)
                    ->withRole($roleBagBuilder->getWerewolf(), 2)
                    ->build()
            )
            ->build()
        ;

        $dispatch = ThereIs::aDispatch()
            ->withEntry($playerBuilders[0], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[1], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[2], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[3], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[4], $roleBagBuilder->getWerewolf())
            ->withEntry($playerBuilders[5], $roleBagBuilder->getWerewolf())
        ;

        $response = When::asUser($hostBuilder)->game()->dispatchRoles($dispatch);
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_role_dispatch(): void
    {
        $hostBuilder = ThereIs::anUser()->withUsername('SLiipMan')->build();
        $hostPlayerBuilder = ThereIs::aPlayer()->withUser($hostBuilder)->build();

        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $playerBuilders = ThereIs::aPlayer()->build(6, true);
        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withGameMaster($hostPlayerBuilder)
            ->withPlayers($playerBuilders)
            ->withInitialisationStep(GameInitialisationStepEnum::DISPATCH)
            ->build()
        ;
        ThereIs::aConfiguration()
            ->forGame($gameBuilder)
            ->withGameMaster()
            ->withoutRandomDispatch()
            ->withComposition(
                ThereIs::aComposition()
                    ->withRole($roleBagBuilder->getVillager(), 4)
                    ->withRole($roleBagBuilder->getWerewolf(), 2)
                    ->build()
            )
            ->build()
        ;

        $dispatch = ThereIs::aDispatch()
            ->withEntry($playerBuilders[0], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[1], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[2], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[3], $roleBagBuilder->getVillager())
            ->withEntry($playerBuilders[4], $roleBagBuilder->getWerewolf())
            ->withEntry($playerBuilders[5], $roleBagBuilder->getWerewolf())
        ;

        When::asUser($hostBuilder)->game()->dispatchRoles($dispatch);
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(GameRoleDispatchEvent::class, $hostBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
