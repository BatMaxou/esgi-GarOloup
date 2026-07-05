<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Game\Role\InfectedRole;
use App\Entity\Game\Role\Interface\WerewolfVoterInterface;
use App\Entity\Game\Role\LoverRole;
use App\Entity\Game\Role\VillagerRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Cupidon\ComplexGameCupidonNight1LoverInfectedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class InfectedLoverTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;

    public function test_an_infected_lover_keeps_its_couple_and_joins_the_werewolves(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameCupidonNight1LoverInfectedStory::class)->execute();
        $firstLoverPlayerBuilder = $story->get($story->getFirstLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $secondLoverPlayerBuilder = $story->get($story->getSecondLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverPlayerId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverPlayerId);
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonNight1LoverInfectedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $gameBuilder = $story->get(ComplexGameCupidonNight1LoverInfectedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $clock->sleep(60);

        When::asUser($cupidonUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $firstLover = $firstLoverPlayerBuilder->getEntity();
        $this->assertFalse($firstLover->isDead());
        $this->assertSame(GameTeamEnum::WEREWOLF, $firstLover->getTeam());

        $loverRole = $firstLover->getRole();
        $this->assertInstanceOf(LoverRole::class, $loverRole);
        $this->assertInstanceOf(InfectedRole::class, $loverRole->getOriginalRole());
        $this->assertInstanceOf(VillagerRole::class, $firstLover->getRoleAs(VillagerRole::class));
        $this->assertInstanceOf(WerewolfVoterInterface::class, $firstLover->getRoleAs(WerewolfVoterInterface::class));
        $this->assertSame($secondLoverPlayerId->toString(), $loverRole->getPartnerPlayerId());

        $this->assertFalse($secondLoverPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameRuntimeStepEnum::DAY, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_an_infected_lover_keeps_its_partner_and_original_role_in_serialization(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameCupidonNight1LoverInfectedStory::class)->execute();
        $firstLoverPlayerBuilder = $story->get($story->getFirstLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverUserBuilder = $firstLoverPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $firstLoverUserBuilder);
        $secondLoverPlayerBuilder = $story->get($story->getSecondLoverState());
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverPlayerId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverPlayerId);

        $clock->sleep(60);

        When::asUser($firstLoverUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $response = When::player()->getCurrent();
        $this->assertSame($secondLoverPlayerId->toString(), $response->get('[role][partnerPlayerId]'));
        $this->assertSame('villager', $response->get('[role][type]'));
    }
}
