<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Assassin\ComplexGameAssassinWinCoupleStory;
use App\Fixtures\Story\ComplexGame\Runtime\Assassin\ComplexGameAssassinWinVote4Story;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class AssassinVictoryTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;

    public function test_assassin_wins_as_last_survivor(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameAssassinWinVote4Story::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinWinVote4Story::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);
        $lastVillagerPlayerBuilder = $story->get(ComplexGameAssassinWinVote4Story::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $lastVillagerPlayerBuilder);
        $lastVillagerPlayerId = $lastVillagerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($lastVillagerPlayerId);
        $gameBuilder = $story->get(ComplexGameAssassinWinVote4Story::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($assassinUserBuilder)->game()->assassinKill($lastVillagerPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertTrue($lastVillagerPlayerBuilder->getEntity()->isDead());
        $this->assertFalse($assassinPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameTeamEnum::SOLO, $game->getWinningTeam());
        $this->assertSame(GameRoleEnum::ASSASSIN, $game->getWinningRole());
        $this->assertSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }

    public function test_couple_wins_when_the_assassin_is_a_lover(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameAssassinWinCoupleStory::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinWinCoupleStory::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);
        $lastVillagerPlayerBuilder = $story->get(ComplexGameAssassinWinCoupleStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $lastVillagerPlayerBuilder);
        $gameBuilder = $story->get(ComplexGameAssassinWinCoupleStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $clock->sleep(60);

        When::asUser($assassinUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertFalse($assassinPlayerBuilder->getEntity()->isDead());
        $this->assertFalse($lastVillagerPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameTeamEnum::COUPLE, $game->getWinningTeam());
        $this->assertSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }
}
