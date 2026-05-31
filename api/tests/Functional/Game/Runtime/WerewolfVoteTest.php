<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\WerewolfVoteEvent;
use App\Entity\Game\Role\WerewolfRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Factory\Game\PlayerFactory;
use App\Fixtures\Story\ClassicGame\ClassicGameLaunchedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameSetupedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class WerewolfVoteTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_werewolf_can_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);
        $villagerPlayerBuilder = $story->get(ClassicGameSetupedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        When::asUser($werewolfUserBuilder)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_anonymous_cannot_vote_on_werewolf_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ClassicGameSetupedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        When::game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_werewolf_cannot_vote_not_valid_uuid(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);

        When::asUser($werewolfUserBuilder)->game()->werewolfVote('omg-i-do-not-exist');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_werewolf_cannot_vote_unknown_player(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);

        $playerBuilder = ThereIs::aPlayer()->build();
        $playerId = $playerBuilder->getEntity()->getId();
        $this->assertNotNull($playerId);

        When::asUser($werewolfUserBuilder)->game()->werewolfVote($playerId->toString());
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_werewolf_cannot_vote_if_not_werewolf_turn(): void
    {
        $story = ThereIs::aStory(ClassicGameLaunchedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameLaunchedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);
        $villagerPlayerBuilder = $story->get(ClassicGameLaunchedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        When::asUser($werewolfUserBuilder)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_werewolf_cannot_vote_if_step_ended(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);
        $villagerPlayerBuilder = $story->get(ClassicGameSetupedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);
        $gameBuilder = $story->get(ClassicGameSetupedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $clock->sleep(60);

        When::asUser($werewolfUserBuilder)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_villager_cannot_vote_on_werewolf_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ClassicGameSetupedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $targetPlayerBuilder = $story->get(ClassicGameSetupedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $targetPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asUser($villagerUserBuilder)->game()->werewolfVote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_werewolf_cannot_vote_on_self(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);
        $werewolfPlayerId = $werewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolfPlayerId);

        When::asUser($werewolfUserBuilder)->game()->werewolfVote($werewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_werewolf_cannot_vote_on_other_werewolf(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);
        $werewolf2PlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_2);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf2PlayerBuilder);
        $werewolf2PlayerId = $werewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolf2PlayerId);

        When::asUser($werewolfUserBuilder)->game()->werewolfVote($werewolf2PlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_werewolves_votes_resolve_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);
        $werewolf2PlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_2);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf2PlayerBuilder);
        $werewolf2UserBuilder = $werewolf2PlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolf2UserBuilder);
        $villagerPlayerBuilder = $story->get(ClassicGameSetupedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);
        $gameBuilder = $story->get(ClassicGameSetupedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($werewolfUserBuilder)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::asUser($werewolf2UserBuilder)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($villagerPlayerBuilder->getEntity()->isDead());
        $this->assertTrue(GameRuntimeStepEnum::DAY === $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_unvoted_werewolf_gets_random_target_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);
        $werewolf2PlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_2);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf2PlayerBuilder);
        $werewolf2UserBuilder = $werewolf2PlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolf2UserBuilder);
        $villagerPlayerBuilder = $story->get(ClassicGameSetupedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);
        $gameBuilder = $story->get(ClassicGameSetupedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($werewolfUserBuilder)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $werewolf2PlayerRole = $werewolf2PlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(WerewolfRole::class, $werewolf2PlayerRole);
        $werewolf2TargetPlayerId = $werewolf2PlayerRole->getTargetPlayerId();

        $this->assertNotNull($werewolf2TargetPlayerId);
        $randomTarget = PlayerFactory::find($werewolf2TargetPlayerId);
        $this->assertTrue($randomTarget->getLinkedGame()?->getId() === $game->getId());
        $this->assertFalse($randomTarget->getRole() instanceof WerewolfRole);
        $this->assertTrue(GameRuntimeStepEnum::DAY === $game->getRuntimeStep());
    }

    public function test_game_event_dispatched_by_werewolf_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);
        $villagerPlayerBuilder = $story->get(ClassicGameSetupedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);
        $gameBuilder = $story->get(ClassicGameSetupedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($werewolfUserBuilder)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(WerewolfVoteEvent::class, $werewolfUserBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }

    // not werewolves turn (need another night role)
    // current werewolf voter dead
    // target player dead
}
