<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
use App\Fixtures\Story\ClassicGame\ClassicGameDay2FinishedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class WinConditionTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;

    public function test_village_wins_when_the_last_werewolf_is_voted_out(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameDay2FinishedStory::class)->execute();
        $villager1PlayerBuilder = $story->get(ClassicGameDay2FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villager1PlayerBuilder);
        $villager1UserBuilder = $villager1PlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villager1UserBuilder);
        $seerPlayerBuilder = $story->get(ClassicGameDay2FinishedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $seerPlayerBuilder);
        $seerUserBuilder = $seerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $seerUserBuilder);
        $werewolfPlayerBuilder = $story->get(ClassicGameDay2FinishedStory::WEREWOLF_2);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfPlayerId = $werewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolfPlayerId);
        $gameBuilder = $story->get(ClassicGameDay2FinishedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($villager1UserBuilder)->game()->vote($werewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::asUser($seerUserBuilder)->game()->vote($werewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertTrue($werewolfPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameTeamEnum::VILLAGE, $game->getWinningTeam());
        $this->assertSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }

    public function test_werewolves_win_when_the_vote_brings_them_to_parity(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameDay2FinishedStory::class)->execute();
        $villager1PlayerBuilder = $story->get(ClassicGameDay2FinishedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villager1PlayerBuilder);
        $villager1UserBuilder = $villager1PlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villager1UserBuilder);
        $villager1PlayerId = $villager1PlayerBuilder->getEntity()->getId();
        $this->assertNotNull($villager1PlayerId);
        $seerPlayerBuilder = $story->get(ClassicGameDay2FinishedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $seerPlayerBuilder);
        $seerUserBuilder = $seerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $seerUserBuilder);
        $seerPlayerId = $seerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($seerPlayerId);
        $gameBuilder = $story->get(ClassicGameDay2FinishedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($villager1UserBuilder)->game()->vote($seerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::asUser($seerUserBuilder)->game()->vote($villager1PlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertSame(GameTeamEnum::WEREWOLF, $game->getWinningTeam());
        $this->assertSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }
}
