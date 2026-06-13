<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameDispatchedStory;
use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameLaunchedStory;
use App\Fixtures\Story\ClassicGame\Runtime\Setup\ClassicGameSetupedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use PHPUnit\Framework\Attributes\DataProvider;

class GetWerewolfTeamTest extends GarOloupApiTestCase
{
    /** @return iterable<array{0: class-string<ClassicGameDispatchedStory>}> */
    public static function initializationStepsProvider(): iterable
    {
        yield [ClassicGameDispatchedStory::class];
        // to complete
    }

    /** @return iterable<array{0: class-string<ClassicGameDispatchedStory>}> */
    public static function activeStepsProvider(): iterable
    {
        yield [ClassicGameLaunchedStory::class];
        yield [ClassicGameSetupedStory::class];
        // to complete
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
        $story = ThereIs::aStory(ClassicGameLaunchedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ClassicGameLaunchedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);

        When::asUser($villagerUserBuilder)->game()->getWerewolfTeam();
        $this->assertResponseStatusCodeSame(403);
    }

    /** @param class-string<ClassicGameDispatchedStory> $storyClass */
    #[DataProvider('initializationStepsProvider')]
    public function test_werewolf_cannot_access_werewolf_team_during_initialization(string $storyClass): void
    {
        $story = ThereIs::aStory($storyClass)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameDispatchedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);

        When::asUser($werewolfUserBuilder)->game()->getWerewolfTeam();
        $this->assertResponseStatusCodeSame(403);
    }

    /** @param class-string<ClassicGameDispatchedStory> $storyClass */
    #[DataProvider('activeStepsProvider')]
    public function test_werewolf_can_access_werewolf_team_during_active_steps(string $storyClass): void
    {
        $story = ThereIs::aStory($storyClass)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameDispatchedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);

        $response = When::asUser($werewolfUserBuilder)->game()->getWerewolfTeam();
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_werewolf_team_contains_all_werewolves_including_self_and_dead_ones(): void
    {
        $callerUserBuilder = ThereIs::anUser()->build();

        $gameBuilder = ThereIs::aGame()->withRuntimeStep(GameRuntimeStepEnum::NIGHT)->build();
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
