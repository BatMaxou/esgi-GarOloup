<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Cupidon\ComplexGameCupidonHunterLoverNight1WitchPassedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Cupidon\ComplexGameCupidonNight1BothLoversKilledStory;
use App\Fixtures\Story\ComplexGame\Runtime\Cupidon\ComplexGameCupidonNight1WitchPassedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class CoupleChainDeathTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;

    public function test_lover_dies_of_grief_when_its_partner_is_killed(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameCupidonNight1WitchPassedStory::class)->execute();
        $firstLoverPlayerBuilder = $story->get($story->getFirstLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $secondLoverPlayerBuilder = $story->get($story->getSecondLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonNight1WitchPassedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $gameBuilder = $story->get(ComplexGameCupidonNight1WitchPassedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $game = $gameBuilder->getEntity();

        $this->assertTrue($firstLoverPlayerBuilder->getEntity()->isDead());
        $this->assertFalse($secondLoverPlayerBuilder->getEntity()->isDead());

        $clock->sleep($game->getMaxTimeForDiscussion());

        When::asUser($cupidonUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($secondLoverPlayerBuilder->getEntity()->isDead());
        $this->assertFalse($cupidonPlayerBuilder->getEntity()->isDead());

        $deadPlayersNumber = 0;
        foreach ($game->getPlayers() as $player) {
            if ($player->isDead()) {
                ++$deadPlayersNumber;
            }
        }
        $this->assertSame(2, $deadPlayersNumber);

        $this->assertNull($game->getWinningTeam());
        $this->assertNotSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }

    public function test_hunter_lover_dies_of_grief_and_gets_its_interrupt(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameCupidonHunterLoverNight1WitchPassedStory::class)->execute();
        $hunterPlayerBuilder = $story->get(ComplexGameCupidonHunterLoverNight1WitchPassedStory::HUNTER);
        $this->assertInstanceOf(PlayerBuilder::class, $hunterPlayerBuilder);
        $hunterUserBuilder = $hunterPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $hunterUserBuilder);
        $werewolfPlayerBuilder = $story->get(ComplexGameCupidonHunterLoverNight1WitchPassedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfPlayerId = $werewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolfPlayerId);
        $gameBuilder = $story->get(ComplexGameCupidonHunterLoverNight1WitchPassedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $game = $gameBuilder->getEntity();

        $this->assertFalse($hunterPlayerBuilder->getEntity()->isDead());

        $clock->sleep($game->getMaxTimeForDiscussion());

        When::asUser($hunterUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($hunterPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameRuntimeStepEnum::INTERRUPT, $game->getRuntimeStep());
        $this->assertSame(GameRoleEnum::HUNTER, $game->getInterruptedByRole());

        When::game()->hunterShoot($werewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($werewolfPlayerBuilder->getEntity()->isDead());
    }

    public function test_no_grief_death_when_both_lovers_die_together(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameCupidonNight1BothLoversKilledStory::class)->execute();
        $firstLoverPlayerBuilder = $story->get($story->getFirstLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $secondLoverPlayerBuilder = $story->get($story->getSecondLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonNight1BothLoversKilledStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $gameBuilder = $story->get(ComplexGameCupidonNight1BothLoversKilledStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);
        $game = $gameBuilder->getEntity();

        $this->assertTrue($firstLoverPlayerBuilder->getEntity()->isDead());
        $this->assertTrue($secondLoverPlayerBuilder->getEntity()->isDead());

        $clock->sleep($game->getMaxTimeForDiscussion());

        When::asUser($cupidonUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $deadPlayersNumber = 0;
        foreach ($game->getPlayers() as $player) {
            if ($player->isDead()) {
                ++$deadPlayersNumber;
            }
        }
        $this->assertSame(2, $deadPlayersNumber);

        $this->assertNull($game->getWinningTeam());
        $this->assertNotSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }
}
