<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\CupidonSetupEvent;
use App\Entity\Game\Player;
use App\Entity\Game\Role\CupidonRole;
use App\Entity\Game\Role\LoverRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Cupidon\ComplexGameCupidonLaunchedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Cupidon\ComplexGameCupidonSetupedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class CupidonSetupTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_cupidon_can_choose_lovers(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $firstLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverId = $firstLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstLoverId);
        $secondLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverId);

        When::asUser($cupidonUserBuilder)->game()->cupidonSetup($firstLoverId->toString(), $secondLoverId->toString());
        $this->assertResponseStatusCodeSame(200);

        $firstLoverRole = $firstLoverPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(LoverRole::class, $firstLoverRole);
        $this->assertSame($secondLoverId->toString(), $firstLoverRole->getPartnerPlayerId());
        $secondLoverRole = $secondLoverPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(LoverRole::class, $secondLoverRole);
        $this->assertSame($firstLoverId->toString(), $secondLoverRole->getPartnerPlayerId());

        $this->assertSame(GameTeamEnum::VILLAGE, $firstLoverPlayerBuilder->getEntity()->getTeam());
        $this->assertSame(GameTeamEnum::VILLAGE, $secondLoverPlayerBuilder->getEntity()->getTeam());

        $cupidonRole = $cupidonPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(CupidonRole::class, $cupidonRole);
        $this->assertTrue($cupidonRole->isSetup());
    }

    public function test_lover_receives_partner_id_and_keeps_original_role(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $firstLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverUserBuilder = $firstLoverPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $firstLoverUserBuilder);
        $firstLoverId = $firstLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstLoverId);
        $secondLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverId);

        When::asUser($cupidonUserBuilder)->game()->cupidonSetup($firstLoverId->toString(), $secondLoverId->toString());
        $this->assertResponseStatusCodeSame(200);

        $response = When::asUser($firstLoverUserBuilder)->player()->getCurrent();
        $this->assertSame($secondLoverId->toString(), $response->get('[role][partnerPlayerId]'));
        $this->assertSame('villager', $response->get('[role][type]'));
    }

    public function test_anonymous_cannot_choose_lovers(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $firstLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverId = $firstLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstLoverId);
        $secondLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverId);

        When::game()->cupidonSetup($firstLoverId->toString(), $secondLoverId->toString());
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_non_cupidon_cannot_choose_lovers(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $firstLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverId = $firstLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstLoverId);
        $secondLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverId);

        When::asUser($villagerUserBuilder)->game()->cupidonSetup($firstLoverId->toString(), $secondLoverId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cupidon_cannot_choose_itself_as_lover(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $cupidonPlayerId = $cupidonPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($cupidonPlayerId);
        $secondLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverId);

        When::asUser($cupidonUserBuilder)->game()->cupidonSetup($cupidonPlayerId->toString(), $secondLoverId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cupidon_cannot_choose_the_same_player_for_both_lovers(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $firstLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverId = $firstLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstLoverId);

        When::asUser($cupidonUserBuilder)->game()->cupidonSetup($firstLoverId->toString(), $firstLoverId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cupidon_cannot_choose_an_unknown_lover(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $secondLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverId);

        When::asUser($cupidonUserBuilder)->game()->cupidonSetup('omg-i-do-not-exist', $secondLoverId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cupidon_cannot_choose_a_lover_from_another_game(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $firstLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverId = $firstLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstLoverId);

        $strangerPlayerBuilder = ThereIs::aPlayer()->build();
        $strangerPlayerId = $strangerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($strangerPlayerId);

        When::asUser($cupidonUserBuilder)->game()->cupidonSetup($firstLoverId->toString(), $strangerPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cupidon_cannot_choose_lovers_if_not_setup_step(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonSetupedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonSetupedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $firstLoverPlayerBuilder = $story->get(ComplexGameCupidonSetupedStory::VILLAGER_4);
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverId = $firstLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstLoverId);
        $secondLoverPlayerBuilder = $story->get(ComplexGameCupidonSetupedStory::VILLAGER_5);
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverId);

        When::asUser($cupidonUserBuilder)->game()->cupidonSetup($firstLoverId->toString(), $secondLoverId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cupidon_cannot_choose_lovers_twice(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $firstLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverId = $firstLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstLoverId);
        $secondLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverId);
        $thirdPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_4);
        $this->assertInstanceOf(PlayerBuilder::class, $thirdPlayerBuilder);
        $thirdPlayerId = $thirdPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($thirdPlayerId);
        $fourthPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_5);
        $this->assertInstanceOf(PlayerBuilder::class, $fourthPlayerBuilder);
        $fourthPlayerId = $fourthPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($fourthPlayerId);

        When::asUser($cupidonUserBuilder)->game()->cupidonSetup($firstLoverId->toString(), $secondLoverId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::game()->cupidonSetup($thirdPlayerId->toString(), $fourthPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_cupidon_cannot_choose_lovers_if_step_ended(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $firstLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverId = $firstLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstLoverId);
        $secondLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverId);

        $clock->sleep(60);

        When::asUser($cupidonUserBuilder)->game()->cupidonSetup($firstLoverId->toString(), $secondLoverId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_game_event_dispatched_by_cupidon_setup(): void
    {
        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $firstLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_2);
        $this->assertInstanceOf(PlayerBuilder::class, $firstLoverPlayerBuilder);
        $firstLoverId = $firstLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($firstLoverId);
        $secondLoverPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::VILLAGER_3);
        $this->assertInstanceOf(PlayerBuilder::class, $secondLoverPlayerBuilder);
        $secondLoverId = $secondLoverPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($secondLoverId);
        $gameBuilder = $story->get(ComplexGameCupidonLaunchedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($cupidonUserBuilder)->game()->cupidonSetup($firstLoverId->toString(), $secondLoverId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(CupidonSetupEvent::class, $cupidonUserBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }

    public function test_random_lovers_are_assigned_on_time_up(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameCupidonLaunchedStory::class)->execute();
        $cupidonPlayerBuilder = $story->get(ComplexGameCupidonLaunchedStory::CUPIDON);
        $this->assertInstanceOf(PlayerBuilder::class, $cupidonPlayerBuilder);
        $cupidonUserBuilder = $cupidonPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $cupidonUserBuilder);
        $gameBuilder = $story->get(ComplexGameCupidonLaunchedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $clock->sleep(60);

        When::asUser($cupidonUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $cupidonRole = $cupidonPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(CupidonRole::class, $cupidonRole);
        $this->assertTrue($cupidonRole->isSetup());

        $lovers = [];
        foreach ($gameBuilder->getEntity()->getPlayers() as $player) {
            $role = $player->getRoleAs(LoverRole::class);
            if ($role instanceof LoverRole) {
                $lovers[] = $player;
            }
        }
        $this->assertCount(2, $lovers);

        [$firstLover, $secondLover] = $lovers;
        $this->assertInstanceOf(Player::class, $firstLover);
        $this->assertInstanceOf(Player::class, $secondLover);
        $firstLoverRole = $firstLover->getRoleAs(LoverRole::class);
        $this->assertInstanceOf(LoverRole::class, $firstLoverRole);
        $secondLoverRole = $secondLover->getRoleAs(LoverRole::class);
        $this->assertInstanceOf(LoverRole::class, $secondLoverRole);
        $this->assertSame($secondLover->getId()?->toString(), $firstLoverRole->getPartnerPlayerId());
        $this->assertSame($firstLover->getId()?->toString(), $secondLoverRole->getPartnerPlayerId());

        $this->assertSame(GameRuntimeStepEnum::NIGHT, $gameBuilder->getEntity()->getRuntimeStep());
    }
}
