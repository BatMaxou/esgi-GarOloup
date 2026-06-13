<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\WildChildSetupEvent;
use App\Entity\Game\Role\WildChildRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ComplexGame\Initialisation\ComplexGameLaunchedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Setup\ComplexGameSetupedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class WildChildSetupTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_wild_child_can_choose_a_model(): void
    {
        $story = ThereIs::aStory(ComplexGameLaunchedStory::class)->execute();
        $wildChildPlayerBuilder = $story->get(ComplexGameLaunchedStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);
        $modelPlayerBuilder = $story->get(ComplexGameLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $modelPlayerBuilder);
        $modelPlayerId = $modelPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($modelPlayerId);

        When::asUser($wildChildUserBuilder)->game()->wildChildSetup($modelPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $response = When::asUser($wildChildUserBuilder)->player()->getCurrent();
        $this->assertSame($modelPlayerId->toString(), $response->get('[role][modelPlayerId]'));
        $this->assertFalse($response->get('[role][transformed]'));
    }

    public function test_anonymous_cannot_choose_a_model(): void
    {
        $story = ThereIs::aStory(ComplexGameLaunchedStory::class)->execute();
        $modelPlayerBuilder = $story->get(ComplexGameLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $modelPlayerBuilder);
        $modelPlayerId = $modelPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($modelPlayerId);

        When::game()->wildChildSetup($modelPlayerId->toString());
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_non_wild_child_cannot_choose_a_model(): void
    {
        $story = ThereIs::aStory(ComplexGameLaunchedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ComplexGameLaunchedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $modelPlayerBuilder = $story->get(ComplexGameLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $modelPlayerBuilder);
        $modelPlayerId = $modelPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($modelPlayerId);

        When::asUser($villagerUserBuilder)->game()->wildChildSetup($modelPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_wild_child_cannot_choose_itself_as_model(): void
    {
        $story = ThereIs::aStory(ComplexGameLaunchedStory::class)->execute();
        $wildChildPlayerBuilder = $story->get(ComplexGameLaunchedStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);
        $wildChildPlayerId = $wildChildPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($wildChildPlayerId);

        When::asUser($wildChildUserBuilder)->game()->wildChildSetup($wildChildPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_wild_child_cannot_choose_an_unknown_target(): void
    {
        $story = ThereIs::aStory(ComplexGameLaunchedStory::class)->execute();
        $wildChildPlayerBuilder = $story->get(ComplexGameLaunchedStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);

        When::asUser($wildChildUserBuilder)->game()->wildChildSetup('omg-i-do-not-exist');
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_wild_child_cannot_choose_a_player_from_another_game(): void
    {
        $story = ThereIs::aStory(ComplexGameLaunchedStory::class)->execute();
        $wildChildPlayerBuilder = $story->get(ComplexGameLaunchedStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);

        $strangerPlayerBuilder = ThereIs::aPlayer()->build();
        $strangerPlayerId = $strangerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($strangerPlayerId);

        When::asUser($wildChildUserBuilder)->game()->wildChildSetup($strangerPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_wild_child_cannot_choose_a_model_if_not_setup_step(): void
    {
        $story = ThereIs::aStory(ComplexGameSetupedStory::class)->execute();
        $wildChildPlayerBuilder = $story->get(ComplexGameSetupedStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);
        $modelPlayerBuilder = $story->get(ComplexGameSetupedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $modelPlayerBuilder);
        $modelPlayerId = $modelPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($modelPlayerId);

        When::asUser($wildChildUserBuilder)->game()->wildChildSetup($modelPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_wild_child_cannot_choose_a_model_if_step_ended(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameLaunchedStory::class)->execute();
        $wildChildPlayerBuilder = $story->get(ComplexGameLaunchedStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);
        $modelPlayerBuilder = $story->get(ComplexGameLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $modelPlayerBuilder);
        $modelPlayerId = $modelPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($modelPlayerId);

        $clock->sleep(60);

        When::asUser($wildChildUserBuilder)->game()->wildChildSetup($modelPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_wild_child_setup(): void
    {
        $story = ThereIs::aStory(ComplexGameLaunchedStory::class)->execute();
        $wildChildPlayerBuilder = $story->get(ComplexGameLaunchedStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);
        $modelPlayerBuilder = $story->get(ComplexGameLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $modelPlayerBuilder);
        $modelPlayerId = $modelPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($modelPlayerId);
        $gameBuilder = $story->get(ComplexGameLaunchedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($wildChildUserBuilder)->game()->wildChildSetup($modelPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(WildChildSetupEvent::class, $wildChildUserBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }

    public function test_a_random_model_is_assigned_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameLaunchedStory::class)->execute();
        $wildChildPlayerBuilder = $story->get(ComplexGameLaunchedStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);
        $gameBuilder = $story->get(ComplexGameLaunchedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $clock->sleep(60);

        When::asUser($wildChildUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $wildChildRole = $wildChildPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(WildChildRole::class, $wildChildRole);
        $this->assertNotNull($wildChildRole->getModelPlayerId());
        $this->assertTrue($wildChildRole->isSetup());
        $this->assertSame(GameRuntimeStepEnum::NIGHT, $gameBuilder->getEntity()->getRuntimeStep());
    }
}
