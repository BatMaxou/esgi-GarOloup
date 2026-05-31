<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\VoteEvent;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ClassicGame\ClassicGameDay1FinishedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameSetupedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class VoteTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_player_can_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voterPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voterPlayerBuilder);
        $voterUserBuilder = $voterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voterUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($voterUserBuilder)->game()->vote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_anonymous_cannot_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $targetPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::game()->vote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_cannot_vote_unvalid_uuid(): void
    {
        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voterPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voterPlayerBuilder);
        $voterUserBuilder = $voterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voterUserBuilder);

        When::asUser($voterUserBuilder)->game()->vote('omg-i-do-not-exist');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_cannot_vote_unknown_player(): void
    {
        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voterPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voterPlayerBuilder);
        $voterUserBuilder = $voterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voterUserBuilder);

        $unknownPlayerBuilder = ThereIs::aPlayer()->build();
        $unknownPlayerId = $unknownPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($unknownPlayerId);

        When::asUser($voterUserBuilder)->game()->vote($unknownPlayerId->toString());
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_cannot_vote_if_not_vote_step(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $voterPlayerBuilder = $story->get(ClassicGameSetupedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $voterPlayerBuilder);
        $voterUserBuilder = $voterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voterUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($voterUserBuilder)->game()->vote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cannot_vote_if_step_ended(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voterPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voterPlayerBuilder);
        $voterUserBuilder = $voterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voterUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        $clock->sleep(60);

        When::asUser($voterUserBuilder)->game()->vote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_dead_player_cannot_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $deadPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $deadPlayerBuilder);
        $this->assertTrue($deadPlayerBuilder->getEntity()->isDead());
        $deadUserBuilder = $deadPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $deadUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($deadUserBuilder)->game()->vote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cannot_vote_on_self(): void
    {
        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voterPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voterPlayerBuilder);
        $voterUserBuilder = $voterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voterUserBuilder);
        $voterPlayerId = $voterPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($voterPlayerId);

        When::asUser($voterUserBuilder)->game()->vote($voterPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cannot_vote_on_dead_player(): void
    {
        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voterPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voterPlayerBuilder);
        $voterUserBuilder = $voterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voterUserBuilder);
        $deadTargetPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $deadTargetPlayerBuilder);
        $this->assertTrue($deadTargetPlayerBuilder->getEntity()->isDead());
        $deadTargetPlayerId = $deadTargetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($deadTargetPlayerId);

        When::asUser($voterUserBuilder)->game()->vote($deadTargetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_revote_replaces_the_previous_target(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voterPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voterPlayerBuilder);
        $voterUserBuilder = $voterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voterUserBuilder);
        $firstTargetPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $firstTargetPlayerBuilder);
        $firstTargetPlayerId = $firstTargetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstTargetPlayerId);
        $secondTargetPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::WEREWOLF_2);
        $this->assertInstanceOf(PlayerBuilder::class, $secondTargetPlayerBuilder);
        $secondTargetPlayerId = $secondTargetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondTargetPlayerId);
        $gameBuilder = $story->get(ClassicGameDay1FinishedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($voterUserBuilder)->game()->vote($firstTargetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::asUser($voterUserBuilder)->game()->vote($secondTargetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::asUser($voterUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($secondTargetPlayerBuilder->getEntity()->isDead());
        $this->assertFalse($firstTargetPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameRuntimeStepEnum::NIGHT, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_votes_resolve_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voter1PlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voter1PlayerBuilder);
        $voter1UserBuilder = $voter1PlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voter1UserBuilder);
        $voter2PlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $voter2PlayerBuilder);
        $voter2UserBuilder = $voter2PlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voter2UserBuilder);
        $targetPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);
        $gameBuilder = $story->get(ClassicGameDay1FinishedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($voter1UserBuilder)->game()->vote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::asUser($voter2UserBuilder)->game()->vote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::asUser($voter1UserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($targetPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameRuntimeStepEnum::NIGHT, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_tie_is_resolved_randomly_between_tied_players(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voter1PlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voter1PlayerBuilder);
        $voter1UserBuilder = $voter1PlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voter1UserBuilder);
        $voter2PlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $voter2PlayerBuilder);
        $voter2UserBuilder = $voter2PlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voter2UserBuilder);
        $tiedTarget1PlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $tiedTarget1PlayerBuilder);
        $tiedTarget1PlayerId = $tiedTarget1PlayerBuilder->getEntity()->getId();
        $this->assertNotNull($tiedTarget1PlayerId);
        $tiedTarget2PlayerBuilder = $story->get(ClassicGameDay1FinishedStory::WEREWOLF_2);
        $this->assertInstanceOf(PlayerBuilder::class, $tiedTarget2PlayerBuilder);
        $tiedTarget2PlayerId = $tiedTarget2PlayerBuilder->getEntity()->getId();
        $this->assertNotNull($tiedTarget2PlayerId);
        $gameBuilder = $story->get(ClassicGameDay1FinishedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($voter1UserBuilder)->game()->vote($tiedTarget1PlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::asUser($voter2UserBuilder)->game()->vote($tiedTarget2PlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::asUser($voter1UserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertNotSame(
            $tiedTarget1PlayerBuilder->getEntity()->isDead(),
            $tiedTarget2PlayerBuilder->getEntity()->isDead(),
        );
        $this->assertSame(GameRuntimeStepEnum::NIGHT, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_no_ballot_eliminates_a_random_player_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voterPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voterPlayerBuilder);
        $voterUserBuilder = $voterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voterUserBuilder);
        $gameBuilder = $story->get(ClassicGameDay1FinishedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $game = $gameBuilder->getEntity();
        $deadBefore = $game->countDeadPlayers();

        $clock->sleep(60);

        When::asUser($voterUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertSame($deadBefore + 1, $game->countDeadPlayers());
        $this->assertSame(GameRuntimeStepEnum::NIGHT, $game->getRuntimeStep());
    }

    public function test_game_event_dispatched_by_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        $voterPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $voterPlayerBuilder);
        $voterUserBuilder = $voterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $voterUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicGameDay1FinishedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);
        $gameBuilder = $story->get(ClassicGameDay1FinishedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($voterUserBuilder)->game()->vote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(VoteEvent::class, $voterUserBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
