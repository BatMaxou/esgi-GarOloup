<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\PassTurnEvent;
use App\Entity\Game\Role\InfectFatherRole;
use App\Entity\Game\Role\WitchRole;
use App\Enum\Game\GameRoleEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Night\InfectFather\ComplexGameNight1InfectFatherPassedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf\ComplexGameNight1WerewolfVotedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class PassTurnTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_witch_can_pass_her_turn_without_using_a_potion(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1InfectFatherPassedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1InfectFatherPassedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);

        When::asUser($witchUserBuilder)->game()->passTurn();
        $this->assertResponseStatusCodeSame(200);

        $witchRole = $witchPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(WitchRole::class, $witchRole);
        $this->assertTrue($witchRole->isHealPotionAvailable());
        $this->assertTrue($witchRole->isPoisonPotionAvailable());
    }

    public function test_infect_father_can_pass_his_turn_without_using_his_infection(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $infectPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::INFECT_FATHER);
        $this->assertInstanceOf(PlayerBuilder::class, $infectPlayerBuilder);
        $infectUserBuilder = $infectPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $infectUserBuilder);

        When::asUser($infectUserBuilder)->game()->passTurn();
        $this->assertResponseStatusCodeSame(200);

        $infectRole = $infectPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(InfectFatherRole::class, $infectRole);
        $this->assertTrue($infectRole->isInfectionAvailable());
    }

    public function test_passing_advances_the_night_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $infectPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::INFECT_FATHER);
        $this->assertInstanceOf(PlayerBuilder::class, $infectPlayerBuilder);
        $infectUserBuilder = $infectPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $infectUserBuilder);
        $gameBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($infectUserBuilder)->game()->passTurn();
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $workflow = $gameBuilder->getEntity()->getNightWorkflow();
        $this->assertNotNull($workflow);
        $this->assertContains(GameRoleEnum::WITCH, $workflow->getCurrentTurn());
    }

    public function test_anonymous_cannot_pass(): void
    {
        ThereIs::aStory(ComplexGameNight1InfectFatherPassedStory::class)->execute();

        When::game()->passTurn();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_player_without_a_passable_role_cannot_pass(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1InfectFatherPassedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ComplexGameNight1InfectFatherPassedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);

        When::asUser($villagerUserBuilder)->game()->passTurn();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_witch_cannot_pass_when_it_is_not_her_turn(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);

        When::asUser($witchUserBuilder)->game()->passTurn();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cannot_pass_once_the_step_has_ended(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight1InfectFatherPassedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1InfectFatherPassedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);

        $clock->sleep(60);

        When::asUser($witchUserBuilder)->game()->passTurn();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_pass_turn(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1InfectFatherPassedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1InfectFatherPassedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $gameBuilder = $story->get(ComplexGameNight1InfectFatherPassedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($witchUserBuilder)->game()->passTurn();
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(PassTurnEvent::class, $witchUserBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
