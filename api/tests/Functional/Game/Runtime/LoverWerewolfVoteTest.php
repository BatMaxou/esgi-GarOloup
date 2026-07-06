<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Game\Role\Interface\WerewolfVoterInterface;
use App\Entity\Game\Role\LoverRole;
use App\Fixtures\Story\ComplexGame\Runtime\Cupidon\ComplexGameCupidonWerewolfLoverNight1SeerPassedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;

class LoverWerewolfVoteTest extends GarOloupApiTestCase
{
    public function test_a_werewolf_lover_votes_through_its_lover_role(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonWerewolfLoverNight1SeerPassedStory::class)->execute();
        $werewolfLoverPlayerBuilder = $story->get(ComplexGameCupidonWerewolfLoverNight1SeerPassedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfLoverPlayerBuilder);
        $werewolfLoverUserBuilder = $werewolfLoverPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $werewolfLoverUserBuilder);
        $targetPlayerBuilder = $story->get(ComplexGameCupidonWerewolfLoverNight1SeerPassedStory::VILLAGER_4);
        $this->assertInstanceOf(PlayerBuilder::class, $targetPlayerBuilder);
        $targetPlayerId = $targetPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($targetPlayerId);

        $werewolfLover = $werewolfLoverPlayerBuilder->getEntity();
        $this->assertInstanceOf(LoverRole::class, $werewolfLover->getRole());

        When::asUser($werewolfLoverUserBuilder)->game()->werewolfVote($targetPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $voterRole = $werewolfLover->getRoleAs(WerewolfVoterInterface::class);
        $this->assertInstanceOf(WerewolfVoterInterface::class, $voterRole);
        $this->assertSame($targetPlayerId->toString(), $voterRole->getTargetPlayerId());
    }
}
