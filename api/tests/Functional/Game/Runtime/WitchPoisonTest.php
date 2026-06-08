<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\WitchPoisonEvent;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight1SeerRevealedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight1WerewolfVotedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class WitchPoisonTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_witch_can_poison_a_player(): void
    {
        $story = ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($witchUserBuilder)->game()->witchPoison($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $response = When::asUser($witchUserBuilder)->player()->getCurrent();
        $this->assertFalse($response->get('[role][poisonPotionAvailable]'));
        $this->assertTrue($response->get('[role][healPotionAvailable]'));
    }

    public function test_anonymous_cannot_poison(): void
    {
        $story = ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        $targetPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::game()->witchPoison($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_non_witch_cannot_poison(): void
    {
        $story = ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($villagerUserBuilder)->game()->witchPoison($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_witch_cannot_poison_invalid_uuid(): void
    {
        $story = ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);

        When::asUser($witchUserBuilder)->game()->witchPoison('omg-i-do-not-exist');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_witch_cannot_poison_player_from_another_game(): void
    {
        $story = ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);

        $strangerPlayerBuilder = ThereIs::aPlayer()->build();
        $strangerPlayerId = $strangerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($strangerPlayerId);

        When::asUser($witchUserBuilder)->game()->witchPoison($strangerPlayerId->toString());
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_witch_cannot_poison_self(): void
    {
        $story = ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $witchPlayerId = $witchPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($witchPlayerId);

        When::asUser($witchUserBuilder)->game()->witchPoison($witchPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_witch_cannot_poison_if_not_witch_turn(): void
    {
        $story = ThereIs::aStory(ClassicWitchGameNight1SeerRevealedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ClassicWitchGameNight1SeerRevealedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicWitchGameNight1SeerRevealedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($witchUserBuilder)->game()->witchPoison($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_witch_cannot_poison_if_step_ended(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        $clock->sleep(60);

        When::asUser($witchUserBuilder)->game()->witchPoison($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_witch_cannot_use_both_potions_in_the_same_night(): void
    {
        $story = ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $victimPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $victimPlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($victimPlayerId);
        $targetPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($witchUserBuilder)->game()->witchSave($victimPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::asUser($witchUserBuilder)->game()->witchPoison($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_poison_kills_the_target_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);
        $gameBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($witchUserBuilder)->game()->witchPoison($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::asUser($witchUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($targetPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameRuntimeStepEnum::DAY, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_game_event_dispatched_by_witch_poison(): void
    {
        $story = ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);
        $gameBuilder = $story->get(ClassicWitchGameNight1WerewolfVotedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($witchUserBuilder)->game()->witchPoison($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(WitchPoisonEvent::class, $witchUserBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
