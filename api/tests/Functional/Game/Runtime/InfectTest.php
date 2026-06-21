<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Game\Role\InfectFatherRole;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Seer\ComplexGameNight1SeerRevealedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf\ComplexGameNight1WerewolfVotedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class InfectTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;

    public function test_infect_father_can_infect_the_werewolf_victim(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $infectFatherPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::INFECT_FATHER);
        $this->assertInstanceOf(PlayerBuilder::class, $infectFatherPlayerBuilder);
        $infectFatherUserBuilder = $infectFatherPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $infectFatherUserBuilder);

        When::asUser($infectFatherUserBuilder)->game()->infect();
        $this->assertResponseStatusCodeSame(200);

        $role = $infectFatherPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(InfectFatherRole::class, $role);
        $this->assertFalse($role->isInfectionAvailable());
    }

    public function test_anonymous_cannot_infect(): void
    {
        ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();

        When::game()->infect();
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_non_infect_father_cannot_infect(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);

        When::asUser($villagerUserBuilder)->game()->infect();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_infect_father_cannot_infect_outside_its_turn(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1SeerRevealedStory::class)->execute();
        $infectFatherPlayerBuilder = $story->get(ComplexGameNight1SeerRevealedStory::INFECT_FATHER);
        $this->assertInstanceOf(PlayerBuilder::class, $infectFatherPlayerBuilder);
        $infectFatherUserBuilder = $infectFatherPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $infectFatherUserBuilder);

        When::asUser($infectFatherUserBuilder)->game()->infect();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_infect_father_cannot_infect_twice(): void
    {
        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $infectFatherPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::INFECT_FATHER);
        $this->assertInstanceOf(PlayerBuilder::class, $infectFatherPlayerBuilder);
        $infectFatherUserBuilder = $infectFatherPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $infectFatherUserBuilder);

        When::asUser($infectFatherUserBuilder)->game()->infect();
        $this->assertResponseStatusCodeSame(200);

        When::game()->infect();
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_infect_father_cannot_infect_if_step_ended(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        $infectFatherPlayerBuilder = $story->get(ComplexGameNight1WerewolfVotedStory::INFECT_FATHER);
        $this->assertInstanceOf(PlayerBuilder::class, $infectFatherPlayerBuilder);
        $infectFatherUserBuilder = $infectFatherPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $infectFatherUserBuilder);

        $clock->sleep(60);

        When::asUser($infectFatherUserBuilder)->game()->infect();
        $this->assertResponseStatusCodeSame(403);
    }
}
