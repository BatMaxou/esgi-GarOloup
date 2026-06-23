<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Game\Role\InfectedRole;
use App\Entity\Game\Role\SeerRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Night\InfectFather\ComplexGameNight1InfectedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class InfectedPowerTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;

    public function test_an_infected_player_survives_keeps_its_role_and_joins_the_werewolves(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight1InfectedStory::class)->execute();
        $victimPlayerBuilder = $story->get(ComplexGameNight1InfectedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $victimPlayerBuilder);
        $villagerPlayerBuilder = $story->get(ComplexGameNight1InfectedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $gameBuilder = $story->get(ComplexGameNight1InfectedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $clock->sleep(60);

        When::asUser($villagerUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $victim = $victimPlayerBuilder->getEntity();
        $this->assertFalse($victim->isDead());
        $this->assertInstanceOf(InfectedRole::class, $victim->getRole());
        $this->assertInstanceOf(SeerRole::class, $victim->getRoleAs(SeerRole::class));
        $this->assertSame(GameTeamEnum::WEREWOLF, $victim->getTeam());
        $this->assertSame(GameRuntimeStepEnum::DAY, $gameBuilder->getEntity()->getRuntimeStep());
    }

    public function test_an_infected_player_appears_in_the_werewolf_team(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight1InfectedStory::class)->execute();
        $victimPlayerBuilder = $story->get(ComplexGameNight1InfectedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $victimPlayerBuilder);
        $victimPlayerId = $victimPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($victimPlayerId);
        $werewolfPlayerBuilder = $story->get(ComplexGameNight1InfectedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfUserBuilder = $werewolfPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfUserBuilder);

        $clock->sleep(60);

        When::asUser($werewolfUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $response = When::game()->getWerewolfTeam();
        $this->assertResponseStatusCodeSame(200);

        $members = $response->get('[members]');
        $this->assertIsArray($members);

        $returnedIds = [];
        foreach ($members as $member) {
            $this->assertIsArray($member);
            $this->assertArrayHasKey('id', $member);
            $returnedIds[] = $member['id'];
        }

        $this->assertContains($victimPlayerId->toString(), $returnedIds);
    }
}
