<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
use App\Fixtures\Story\ComplexGame\Runtime\HunterEndgame\ComplexGameHunterEndgameVoteStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class HunterInterruptWinConditionTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;

    public function test_game_does_not_finish_while_the_hunter_shot_is_pending(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameHunterEndgameVoteStory::class)->execute();

        $villagerPlayerBuilder = $story->get(ComplexGameHunterEndgameVoteStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);

        $wildChildPlayerBuilder = $story->get(ComplexGameHunterEndgameVoteStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);

        $hunterPlayerBuilder = $story->get(ComplexGameHunterEndgameVoteStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterPlayerId = $hunterPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($hunterPlayerId);

        $gameBuilder = $story->get(ComplexGameHunterEndgameVoteStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($villagerUserBuilder)->game()->vote($hunterPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);
        When::asUser($wildChildUserBuilder)->game()->vote($hunterPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertTrue($hunterPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameRuntimeStepEnum::INTERRUPT, $game->getRuntimeStep());
        $this->assertNull($game->getWinningTeam());
        $this->assertSame(GameRoleEnum::HUNTER, $game->getInterruptedByRole());
    }

    public function test_the_hunter_shot_can_still_hand_the_win_to_the_village(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameHunterEndgameVoteStory::class)->execute();

        $villagerPlayerBuilder = $story->get(ComplexGameHunterEndgameVoteStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);

        $wildChildPlayerBuilder = $story->get(ComplexGameHunterEndgameVoteStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);
        $wildChildPlayerId = $wildChildPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($wildChildPlayerId);

        $hunterPlayerBuilder = $story->get(ComplexGameHunterEndgameVoteStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);
        $hunterPlayerId = $hunterPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($hunterPlayerId);

        $gameBuilder = $story->get(ComplexGameHunterEndgameVoteStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($villagerUserBuilder)->game()->vote($hunterPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);
        When::asUser($wildChildUserBuilder)->game()->vote($hunterPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        When::asUser($hunterUserBuilder)->game()->hunterShoot($wildChildPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);
        $this->assertTrue($wildChildPlayerBuilder->getEntity()->isDead());

        $clock->sleep(30);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertSame(GameTeamEnum::VILLAGE, $game->getWinningTeam());
        $this->assertSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }

    public function test_the_werewolf_win_is_declared_once_the_hunter_shot_is_resolved(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameHunterEndgameVoteStory::class)->execute();

        $villagerPlayerBuilder = $story->get(ComplexGameHunterEndgameVoteStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $villagerPlayerId = $villagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villagerPlayerId);

        $wildChildPlayerBuilder = $story->get(ComplexGameHunterEndgameVoteStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);

        $hunterPlayerBuilder = $story->get(ComplexGameHunterEndgameVoteStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);
        $hunterPlayerId = $hunterPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($hunterPlayerId);

        $gameBuilder = $story->get(ComplexGameHunterEndgameVoteStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($villagerUserBuilder)->game()->vote($hunterPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);
        When::asUser($wildChildUserBuilder)->game()->vote($hunterPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        When::asUser($hunterUserBuilder)->game()->hunterShoot($villagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);
        $this->assertTrue($villagerPlayerBuilder->getEntity()->isDead());

        $clock->sleep(30);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertSame(GameTeamEnum::WEREWOLF, $game->getWinningTeam());
        $this->assertSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }
}
