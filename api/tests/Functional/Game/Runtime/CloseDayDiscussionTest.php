<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\TimeUpGameEvent;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ClassicGame\Runtime\Night\Werewolf\ClassicGameNight1WerewolfVotedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class CloseDayDiscussionTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_day_discussion_closes_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameNight1WerewolfVotedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ClassicGameNight1WerewolfVotedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $gameBuilder = $story->get(ClassicGameNight1WerewolfVotedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $game = $gameBuilder->getEntity();
        $this->assertEquals(GameRuntimeStepEnum::DAY, $game->getRuntimeStep());

        $clock->sleep($game->getMaxTimeForDiscussion());

        When::asUser($villagerUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertEquals(GameRuntimeStepEnum::VOTE, $game->getRuntimeStep());
        $this->assertNull($game->getCurrentDay());
    }

    public function test_anonymous_cannot_close_day_discussion(): void
    {
        ThereIs::aStory(ClassicGameNight1WerewolfVotedStory::class)->execute();

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_cannot_close_day_discussion_before_timer_expires(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameNight1WerewolfVotedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ClassicGameNight1WerewolfVotedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $gameBuilder = $story->get(ClassicGameNight1WerewolfVotedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $clock->sleep($gameBuilder->getEntity()->getMaxTimeForDiscussion() - 1);

        When::asUser($villagerUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(409);

        $this->assertEquals(GameRuntimeStepEnum::DAY, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_game_event_dispatched_by_close_day_discussion(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameNight1WerewolfVotedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ClassicGameNight1WerewolfVotedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $gameBuilder = $story->get(ClassicGameNight1WerewolfVotedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $game = $gameBuilder->getEntity();
        $clock->sleep($game->getMaxTimeForDiscussion() + 1);

        When::asUser($villagerUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(TimeUpGameEvent::class, $villagerUserBuilder->username, $game->getId());
        $this->assertEventCollectedNumber(1);
    }
}
