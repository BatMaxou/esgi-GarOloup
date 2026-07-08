<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Fixtures\Story\ComplexGame\Runtime\Assassin\ComplexGameAssassinDayVoteKillsAssassinStory;
use App\Fixtures\Story\ComplexGame\Runtime\Assassin\ComplexGameAssassinNight1InfectTurnTargetAssassinStory;
use App\Fixtures\Story\ComplexGame\Runtime\Assassin\ComplexGameAssassinNight1WitchTurnStory;
use App\Fixtures\Story\ComplexGame\Runtime\Assassin\ComplexGameAssassinNight2WerewolvesTargetAssassinStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class AssassinImmunityTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;

    public function test_werewolves_cannot_kill_the_assassin_at_night(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameAssassinNight2WerewolvesTargetAssassinStory::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight2WerewolvesTargetAssassinStory::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinUserBuilder = $assassinPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $assassinUserBuilder);

        $clock->sleep(60);

        When::asUser($assassinUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertFalse($assassinPlayerBuilder->getEntity()->isDead());
    }

    public function test_witch_poison_kills_the_assassin(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameAssassinNight1WitchTurnStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameAssassinNight1WitchTurnStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight1WitchTurnStory::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $assassinPlayerId = $assassinPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($assassinPlayerId);

        When::asUser($witchUserBuilder)->game()->witchPoison($assassinPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $this->assertTrue($assassinPlayerBuilder->getEntity()->isDead());
    }

    public function test_day_vote_kills_the_assassin(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinDayVoteKillsAssassinStory::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinDayVoteKillsAssassinStory::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);

        $this->assertTrue($assassinPlayerBuilder->getEntity()->isDead());
    }

    public function test_infect_father_cannot_infect_the_assassin(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinNight1InfectTurnTargetAssassinStory::class)->execute();
        $infectFatherPlayerBuilder = $story->get(ComplexGameAssassinNight1InfectTurnTargetAssassinStory::INFECT_FATHER);
        $this->assertInstanceOf(PlayerBuilder::class, $infectFatherPlayerBuilder);
        $infectFatherUserBuilder = $infectFatherPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $infectFatherUserBuilder);
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight1InfectTurnTargetAssassinStory::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);

        When::asUser($infectFatherUserBuilder)->game()->infect();
        $this->assertResponseStatusCodeSame(403);

        $this->assertFalse($assassinPlayerBuilder->getEntity()->isDead());
    }
}
