<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\WerewolfVoteEvent;
use App\Entity\Game\Role\WerewolfRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Factory\Game\GameFactory;
use App\Fixtures\Factory\Game\PlayerFactory;
use App\Fixtures\Story\ClassicGame\ClassicGameConfiguredStory;
use App\Fixtures\Story\ClassicGame\ClassicGameNight1WerewolfTurnStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;

class WerewolfVoteTest extends GarOloupApiTestCase
{
    use GameEventAwareTrait;

    public function test_werewolf_can_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $werewolfPlayerBuilder = $story::class::get($story::WEREWOLF1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfTempUser = $werewolfPlayerBuilder->tempUser;
        $this->assertNotNull($werewolfTempUser);

        $villagerPlayerBuilder = $story::class::get($story::VILLAGER1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        When::asTempUser($werewolfTempUser)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);
    }

    public function test_anonymous_cannot_vote_on_werewolf_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $targetPlayerBuilder = $story::class::get($story::VILLAGER2);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::game()->werewolfVote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_werewolf_cannot_vote_not_valid_uuid(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $werewolfPlayerBuilder = $story::class::get($story::WEREWOLF1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfTempUser = $werewolfPlayerBuilder->tempUser;
        $this->assertNotNull($werewolfTempUser);

        When::asTempUser($werewolfTempUser)->game()->werewolfVote('omg-i-do-not-exist');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_werewolf_cannot_vote_unknown_player(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $werewolfPlayerBuilder = $story::class::get($story::WEREWOLF1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfTempUser = $werewolfPlayerBuilder->tempUser;
        $this->assertNotNull($werewolfTempUser);

        $playerBuilder = ThereIs::aPlayer()->build();
        $playerId = $playerBuilder->getEntity()->getId();
        $this->assertNotNull($playerId);

        When::asTempUser($werewolfTempUser)->game()->werewolfVote($playerId->toString());
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_werewolf_cannot_vote_if_not_werewolf_turn(): void
    {
        $story = ThereIs::aStory(ClassicGameConfiguredStory::class);

        $werewolfPlayerBuilder = $story::class::get($story::WEREWOLF1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfTempUser = $werewolfPlayerBuilder->tempUser;
        $this->assertNotNull($werewolfTempUser);

        $villagerPlayerBuilder = $story::class::get($story::VILLAGER1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        When::asTempUser($werewolfTempUser)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_werewolf_cannot_vote_if_step_ended(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $werewolfPlayerBuilder = $story::class::get($story::WEREWOLF1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfTempUser = $werewolfPlayerBuilder->tempUser;
        $this->assertNotNull($werewolfTempUser);

        $villagerPlayerBuilder = $story::class::get($story::VILLAGER1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        $gameBuilder = $story::class::get($story::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $gameBuilder->getEntity()->setStepEndAt(new \DateTimeImmutable('-1 minute'));
        $this->getEntityManager()->flush();

        When::asTempUser($werewolfTempUser)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_villager_cannot_vote_on_werewolf_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $villagerPlayerBuilder = $story::class::get($story::VILLAGER2);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerTempUser = $villagerPlayerBuilder->tempUser;
        $this->assertNotNull($villagerTempUser);

        $targetPlayerBuilder = $story::class::get($story::VILLAGER3);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        When::asTempUser($villagerTempUser)->game()->werewolfVote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_werewolf_cannot_vote_on_self(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $werewolfPlayerBuilder = $story::class::get($story::WEREWOLF1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfTempUser = $werewolfPlayerBuilder->tempUser;
        $this->assertNotNull($werewolfTempUser);
        $werewolfPlayerId = $werewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolfPlayerId);

        When::asTempUser($werewolfTempUser)->game()->werewolfVote($werewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_werewolf_cannot_vote_on_other_werewolf(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $werewolf1PlayerBuilder = $story::class::get($story::WEREWOLF1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf1PlayerBuilder);
        $werewolf1TempUser = $werewolf1PlayerBuilder->tempUser;
        $this->assertNotNull($werewolf1TempUser);

        $werewolf2PlayerBuilder = $story::class::get($story::WEREWOLF2);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf2PlayerBuilder);
        $werewolf2PlayerId = $werewolf2PlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolf2PlayerId);

        When::asTempUser($werewolf1TempUser)->game()->werewolfVote($werewolf2PlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_werewolves_votes_resolve_on_time_up(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $werewolf1PlayerBuilder = $story::class::get($story::WEREWOLF1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf1PlayerBuilder);
        $werewolf1TempUser = $werewolf1PlayerBuilder->tempUser;
        $this->assertNotNull($werewolf1TempUser);

        $werewolf2PlayerBuilder = $story::class::get($story::WEREWOLF2);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf2PlayerBuilder);
        $werewolf2TempUser = $werewolf2PlayerBuilder->tempUser;
        $this->assertNotNull($werewolf2TempUser);

        $villagerPlayerBuilder = $story::class::get($story::VILLAGER2);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        When::asTempUser($werewolf1TempUser)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::asTempUser($werewolf2TempUser)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $gameBuilder = $story::class::get($story::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        // refetch game after HTTP actions to sync mutation in futur HTTP call
        $game = GameFactory::find($gameBuilder->getEntity()->getId());
        $game->setStepEndAt(new \DateTimeImmutable('-1 minute'));
        $this->getEntityManager()->flush();

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $target = PlayerFactory::find($villagerPlayerId);
        $this->assertTrue($target->isDead());
        $this->assertTrue(GameRuntimeStepEnum::DAY === $game->getRuntimeStep());
    }

    public function test_unvoted_werewolf_gets_random_target_on_time_up(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $werewolf1PlayerBuilder = $story::class::get($story::WEREWOLF1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf1PlayerBuilder);
        $werewolf1TempUser = $werewolf1PlayerBuilder->tempUser;
        $this->assertNotNull($werewolf1TempUser);

        $villagerPlayerBuilder = $story::class::get($story::VILLAGER2);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        When::asTempUser($werewolf1TempUser)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $gameBuilder = $story::class::get($story::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $game = GameFactory::find($gameBuilder->getEntity()->getId());
        $game->setStepEndAt(new \DateTimeImmutable('-1 minute'));
        $this->getEntityManager()->flush();

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $werewolf2PlayerBuilder = $story::class::get($story::WEREWOLF2);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf2PlayerBuilder);
        $werewolf2Role = $werewolf2PlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(WerewolfRole::class, $werewolf2Role);

        $this->assertNotNull($werewolf2Role->getTargetPlayerId());
        $randomTarget = PlayerFactory::find($werewolf2Role->getTargetPlayerId());
        $this->assertTrue($randomTarget->getLinkedGame()?->getId() === $game->getId());
        $this->assertFalse($randomTarget->getRole() instanceof WerewolfRole);
        $this->assertTrue(GameRuntimeStepEnum::DAY === $game->getRuntimeStep());
    }

    public function test_game_event_dispatched_by_werewolf_vote(): void
    {
        $story = ThereIs::aStory(ClassicGameNight1WerewolfTurnStory::class);

        $werewolfPlayerBuilder = $story::class::get($story::WEREWOLF1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfTempUser = $werewolfPlayerBuilder->tempUser;
        $this->assertNotNull($werewolfTempUser);

        $villagerPlayerBuilder = $story::class::get($story::VILLAGER1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        $gameBuilder = $story::class::get($story::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asTempUser($werewolfTempUser)->game()->werewolfVote($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(WerewolfVoteEvent::class, $werewolfTempUser->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }

    // not werewolves turn (need another night role)
    // current werewolf voter dead
    // target player dead
}
