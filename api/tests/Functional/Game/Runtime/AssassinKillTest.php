<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\AssassinKillEvent;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Assassin\ComplexGameAssassinNight1WitchTurnStory;
use App\Fixtures\Story\ComplexGame\Runtime\Assassin\ComplexGameAssassinNight2Story;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class AssassinKillTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_assassin_can_kill_a_player(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinNight2Story::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);
        $targetPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($assassinUserBuilder)->game()->assassinKill($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_anonymous_cannot_kill(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinNight2Story::class)->execute();
        $targetPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::game()->assassinKill($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_non_assassin_cannot_kill(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinNight2Story::class)->execute();
        $villagerPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $targetPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($villagerUserBuilder)->game()->assassinKill($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_assassin_cannot_kill_invalid_uuid(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinNight2Story::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);

        When::asUser($assassinUserBuilder)->game()->assassinKill('omg-i-do-not-exist');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_assassin_cannot_kill_player_from_another_game(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinNight2Story::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);

        $strangerPlayerBuilder = ThereIs::aPlayer()->build();
        $strangerPlayerId = $strangerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($strangerPlayerId);

        When::asUser($assassinUserBuilder)->game()->assassinKill($strangerPlayerId->toString());
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_assassin_cannot_kill_self(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinNight2Story::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);
        $assassinPlayerId = $assassinPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($assassinPlayerId);

        When::asUser($assassinUserBuilder)->game()->assassinKill($assassinPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_assassin_cannot_kill_on_first_night(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinNight1WitchTurnStory::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight1WitchTurnStory::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);
        $targetPlayerBuilder = $story->get(ComplexGameAssassinNight1WitchTurnStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($assassinUserBuilder)->game()->assassinKill($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_assassin_cannot_kill_if_step_ended(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameAssassinNight2Story::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);
        $targetPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        $clock->sleep(60);

        When::asUser($assassinUserBuilder)->game()->assassinKill($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_kill_kills_the_target_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameAssassinNight2Story::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);
        $targetPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);
        $gameBuilder = $story->get(ComplexGameAssassinNight2Story::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($assassinUserBuilder)->game()->assassinKill($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($targetPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameRuntimeStepEnum::DAY, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_game_event_dispatched_by_assassin_kill(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinNight2Story::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);
        $targetPlayerBuilder = $story->get(ComplexGameAssassinNight2Story::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);
        $gameBuilder = $story->get(ComplexGameAssassinNight2Story::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($assassinUserBuilder)->game()->assassinKill($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(AssassinKillEvent::class, $assassinUserBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
