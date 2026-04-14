<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Enum\Game\GameStepEnum;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use PHPUnit\Framework\Attributes\DataProvider;

class GetWerewolfTeamTest extends GarOloupApiTestCase
{
    /** @return iterable<array{0: GameStepEnum}> */
    public static function initializationStepsProvider(): iterable
    {
        yield [GameStepEnum::NEW];
        yield [GameStepEnum::CONFIGURATION];
        yield [GameStepEnum::GAME_MASTER_CHOICE];
        yield [GameStepEnum::DISPATCH];
        yield [GameStepEnum::READY];
    }

    /** @return iterable<array{0: GameStepEnum}> */
    public static function activeStepsProvider(): iterable
    {
        yield [GameStepEnum::SETUP];
        yield [GameStepEnum::NIGHT];
        yield [GameStepEnum::DAY];
        yield [GameStepEnum::VOTE];
    }

    public function test_anonymous_cannot_access_werewolf_team(): void
    {
        When::game()->getWerewolfTeam();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_user_without_game_cannot_access_werewolf_team(): void
    {
        $userBuilder = ThereIs::anUser()->build();

        When::asUser($userBuilder)->game()->getWerewolfTeam();
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_villager_cannot_access_werewolf_team(): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $roleBagBuilder = ThereIs::aRoleBag()->build();
        $gameRoleBagBuilder = ThereIs::aGameRoleBag($roleBagBuilder)->build();

        $gameBuilder = ThereIs::aGame()->withStep(GameStepEnum::NIGHT)->build();
        ThereIs::aPlayer()
            ->withUser($userBuilder)
            ->withRole($gameRoleBagBuilder->getVillager())
            ->withGame($gameBuilder)
            ->build()
        ;

        When::asUser($userBuilder)->game()->getWerewolfTeam();
        $this->assertResponseStatusCodeSame(403);
    }

    #[DataProvider('initializationStepsProvider')]
    public function test_werewolf_cannot_access_werewolf_team_during_initialization(GameStepEnum $step): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $roleBagBuilder = ThereIs::aRoleBag()->build();
        $gameRoleBagBuilder = ThereIs::aGameRoleBag($roleBagBuilder)->build();

        $gameBuilder = ThereIs::aGame()->withStep($step)->build();
        ThereIs::aPlayer()
            ->withUser($userBuilder)
            ->withRole($gameRoleBagBuilder->getWerewolf())
            ->withGame($gameBuilder)
            ->build()
        ;

        When::asUser($userBuilder)->game()->getWerewolfTeam();
        $this->assertResponseStatusCodeSame(403);
    }

    #[DataProvider('activeStepsProvider')]
    public function test_werewolf_can_access_werewolf_team_during_active_steps(GameStepEnum $step): void
    {
        $userBuilder = ThereIs::anUser()->build();
        $roleBagBuilder = ThereIs::aRoleBag()->build();
        $gameRoleBagBuilder = ThereIs::aGameRoleBag($roleBagBuilder)->build();

        $gameBuilder = ThereIs::aGame()->withStep($step)->build();
        $playerBuilder = ThereIs::aPlayer()
            ->withUser($userBuilder)
            ->withRole($gameRoleBagBuilder->getWerewolf())
            ->withGame($gameBuilder)
            ->build()
        ;

        $response = When::asUser($userBuilder)->game()->getWerewolfTeam();
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_werewolf_team_contains_all_werewolves_including_self_and_dead_ones(): void
    {
        $callerUserBuilder = ThereIs::anUser()->build();

        $gameBuilder = ThereIs::aGame()->withStep(GameStepEnum::NIGHT)->build();
        $roleBagBuilder = ThereIs::aRoleBag()->build();
        $gameRoleBagBuilder = ThereIs::aGameRoleBag($roleBagBuilder)->build();

        $callerPlayer = ThereIs::aPlayer()
            ->withUser($callerUserBuilder)
            ->withRole($gameRoleBagBuilder->getWerewolf())
            ->withGame($gameBuilder)
            ->build()
        ;
        $aliveWolf = ThereIs::aPlayer()
            ->withRole($gameRoleBagBuilder->getWerewolf())
            ->withGame($gameBuilder)
            ->build()
        ;
        $deadWolf = ThereIs::aPlayer()
            ->withRole($gameRoleBagBuilder->getWerewolf())
            ->withGame($gameBuilder)
            ->dead()
            ->build()
        ;
        $aliveVillager = ThereIs::aPlayer()
            ->withRole($gameRoleBagBuilder->getVillager())
            ->withGame($gameBuilder)
            ->build()
        ;
        $deadVillager = ThereIs::aPlayer()
            ->withRole($gameRoleBagBuilder->getVillager())
            ->withGame($gameBuilder)
            ->dead()
            ->build()
        ;

        $response = When::asUser($callerUserBuilder)->game()->getWerewolfTeam();
        $this->assertResponseStatusCodeSame(200);

        $members = $response->get('[members]');
        $this->assertIsArray($members);
        $this->assertCount(3, $members);

        $returnedIds = [];
        foreach ($members as $member) {
            $this->assertIsArray($member);
            $this->assertArrayHasKey('id', $member);
            $returnedIds[] = $member['id'];
        }

        $this->assertContains((string) $callerPlayer->getEntity()->getId(), $returnedIds);
        $this->assertContains((string) $aliveWolf->getEntity()->getId(), $returnedIds);
        $this->assertContains((string) $deadWolf->getEntity()->getId(), $returnedIds);
        $this->assertNotContains((string) $aliveVillager->getEntity()->getId(), $returnedIds);
        $this->assertNotContains((string) $deadVillager->getEntity()->getId(), $returnedIds);
    }
}
