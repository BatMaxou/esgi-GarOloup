<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\HunterShootEvent;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Hunter\ComplexGameNight1HunterInterruptStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Hunter\ComplexGameNight1WerewolfKilledHunterStory;
use App\Fixtures\Story\ComplexGame\Runtime\Vote\ComplexGameVote1HunterInterruptStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class HunterShootTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_hunter_can_shoot_a_target_after_night_death(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight1HunterInterruptStory::class)->execute();
        $hunterPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);

        $targetPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        $gameBuilder = $story->get(ComplexGameNight1HunterInterruptStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($hunterUserBuilder)->game()->hunterShoot($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($targetPlayerBuilder->getEntity()->isDead());

        $clock->sleep(30);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertSame(GameRuntimeStepEnum::DAY, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_game_resumes_to_night_after_vote_death(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameVote1HunterInterruptStory::class)->execute();
        $hunterPlayerBuilder = $story->get(ComplexGameVote1HunterInterruptStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);

        $targetPlayerBuilder = $story->get(ComplexGameVote1HunterInterruptStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        $gameBuilder = $story->get(ComplexGameVote1HunterInterruptStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($hunterUserBuilder)->game()->hunterShoot($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($targetPlayerBuilder->getEntity()->isDead());

        $clock->sleep(30);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertSame(GameRuntimeStepEnum::NIGHT, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_timeout_resumes_game_without_killing(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight1HunterInterruptStory::class)->execute();
        $hunterPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);

        $gameBuilder = $story->get(ComplexGameNight1HunterInterruptStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $clock->sleep(30);

        When::asUser($hunterUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertSame(GameRuntimeStepEnum::DAY, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_anonymous_cannot_shoot(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfKilledHunterStory::class)->execute();
        $targetPlayerBuilder = $story->get(ComplexGameNight1WerewolfKilledHunterStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::game()->hunterShoot($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_non_hunter_cannot_shoot(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1HunterInterruptStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);

        $targetPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($villagerUserBuilder)->game()->hunterShoot($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_hunter_cannot_shoot_invalid_uuid(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1HunterInterruptStory::class)->execute();
        $hunterPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);

        When::asUser($hunterUserBuilder)->game()->hunterShoot('not-a-valid-uuid');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_hunter_cannot_shoot_player_from_another_game(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1HunterInterruptStory::class)->execute();
        $hunterPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);

        $strangerPlayerBuilder = ThereIs::aPlayer()->build();
        $strangerPlayerId = $strangerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($strangerPlayerId);

        When::asUser($hunterUserBuilder)->game()->hunterShoot($strangerPlayerId->toString());
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_hunter_cannot_shoot_when_not_interrupt_step(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfKilledHunterStory::class)->execute();
        $hunterPlayerBuilder = $story->get(ComplexGameNight1WerewolfKilledHunterStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);

        $targetPlayerBuilder = $story->get(ComplexGameNight1WerewolfKilledHunterStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($hunterUserBuilder)->game()->hunterShoot($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_hunter_cannot_shoot_after_interrupt_timer_expired(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight1HunterInterruptStory::class)->execute();
        $hunterPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);

        $targetPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        $clock->sleep(31);

        When::asUser($hunterUserBuilder)->game()->hunterShoot($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_hunter_shoot(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1HunterInterruptStory::class)->execute();
        $hunterPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);

        $targetPlayerBuilder = $story->get(ComplexGameNight1HunterInterruptStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        $gameBuilder = $story->get(ComplexGameNight1HunterInterruptStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($hunterUserBuilder)->game()->hunterShoot($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(HunterShootEvent::class, $hunterUserBuilder->username, $gameBuilder->getEntity()->getId()?->toString());
        $this->assertEventCollectedNumber(1);
    }
}
