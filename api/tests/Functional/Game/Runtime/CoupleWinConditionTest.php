<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Cupidon\ComplexGameCupidonCoupleWinStory;
use App\Fixtures\Story\ComplexGame\Runtime\Cupidon\ComplexGameCupidonGriefEndgameStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class CoupleWinConditionTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;

    public function test_couple_wins_when_the_lovers_are_the_last_survivors(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameCupidonCoupleWinStory::class)->execute();
        $firstLoverPlayerBuilder = $story->get($story->getFirstLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $secondLoverPlayerBuilder = $story->get($story->getSecondLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonCoupleWinStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $witchPlayerBuilder = $story->get(ComplexGameCupidonCoupleWinStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $gameBuilder = $story->get(ComplexGameCupidonCoupleWinStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $game = $gameBuilder->getEntity();

        $this->assertSame(GameTeamEnum::WEREWOLF, $firstLoverPlayerBuilder->getEntity()->getTeam());
        $this->assertSame(GameTeamEnum::VILLAGE, $secondLoverPlayerBuilder->getEntity()->getTeam());

        $clock->sleep(60);

        When::asUser($cupidonUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($witchPlayerBuilder->getEntity()->isDead());
        $this->assertFalse($firstLoverPlayerBuilder->getEntity()->isDead());
        $this->assertFalse($secondLoverPlayerBuilder->getEntity()->isDead());
        $this->assertFalse($cupidonPlayerBuilder->getEntity()->isDead());

        $this->assertSame(GameTeamEnum::COUPLE, $game->getWinningTeam());
        $this->assertSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }

    public function test_werewolves_win_when_the_grief_death_breaks_the_couple(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameCupidonGriefEndgameStory::class)->execute();
        $firstLoverPlayerBuilder = $story->get($story->getFirstLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $secondLoverPlayerBuilder = $story->get($story->getSecondLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonGriefEndgameStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $gameBuilder = $story->get(ComplexGameCupidonGriefEndgameStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $game = $gameBuilder->getEntity();

        $this->assertFalse($firstLoverPlayerBuilder->getEntity()->isDead());
        $this->assertFalse($secondLoverPlayerBuilder->getEntity()->isDead());

        $clock->sleep(60);

        When::asUser($cupidonUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($firstLoverPlayerBuilder->getEntity()->isDead());
        $this->assertTrue($secondLoverPlayerBuilder->getEntity()->isDead());
        $this->assertFalse($cupidonPlayerBuilder->getEntity()->isDead());

        $this->assertSame(GameTeamEnum::WEREWOLF, $game->getWinningTeam());
        $this->assertSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }
}
