<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\WitchSaveEvent;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Seer\ComplexGameNight1SeerRevealedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf\ComplexGameNight1WerewolfVotedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class WitchSaveTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_witch_can_save_a_murder_victim(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $victimPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $victimPlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($victimPlayerId);

        When::asUser($witchUserBuilder)->game()->witchSave($victimPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $response = When::player()->getCurrent();
        $this->assertFalse($response->get('[role][healPotionAvailable]'));
        $this->assertTrue($response->get('[role][poisonPotionAvailable]'));
    }

    public function test_anonymous_cannot_save(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $victimPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $victimPlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($victimPlayerId);

        When::game()->witchSave($victimPlayerId->toString());
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_non_witch_cannot_save(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $victimPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $victimPlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($victimPlayerId);

        When::asUser($villagerUserBuilder)->game()->witchSave($victimPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_witch_cannot_save_invalid_uuid(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);

        When::asUser($witchUserBuilder)->game()->witchSave('omg-i-do-not-exist');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_witch_cannot_save_player_from_another_game(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);

        $strangerPlayerBuilder = ThereIs::aPlayer()->build();
        $strangerPlayerId = $strangerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($strangerPlayerId);

        When::asUser($witchUserBuilder)->game()->witchSave($strangerPlayerId->toString());
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_witch_cannot_save_a_non_murder_victim(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $safePlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $safePlayerBuilder);
        $safePlayerId = $safePlayerBuilder->getEntity()->getId();
        $this->assertNotNull($safePlayerId);

        When::asUser($witchUserBuilder)->game()->witchSave($safePlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_witch_cannot_save_twice_the_same_night(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $victimPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $victimPlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($victimPlayerId);

        When::asUser($witchUserBuilder)->game()->witchSave($victimPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::game()->witchSave($victimPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_witch_cannot_save_if_not_witch_turn(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1SeerRevealedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1SeerRevealedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $villagerPlayerBuilder = $story->get(ComplexGameNight1SeerRevealedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        When::asUser($witchUserBuilder)->game()->witchSave($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_witch_cannot_save_if_step_ended(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $victimPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $victimPlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($victimPlayerId);

        $clock->sleep(60);

        When::asUser($witchUserBuilder)->game()->witchSave($victimPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_save_revives_the_victim_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $victimPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $victimPlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($victimPlayerId);
        $gameBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($witchUserBuilder)->game()->witchSave($victimPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertFalse($victimPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameRuntimeStepEnum::DAY, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_game_event_dispatched_by_witch_save(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $victimPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $victimPlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($victimPlayerId);
        $gameBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($witchUserBuilder)->game()->witchSave($victimPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(WitchSaveEvent::class, $witchUserBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
