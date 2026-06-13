<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Event\Game\SeerRevealEvent;
use App\Enum\Game\GameRoleEnum;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ClassicGame\Runtime\Setup\ClassicGameSetupedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\Trait\GameEventAwareTrait;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class SeerRevealTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;
    use GameEventAwareTrait;

    public function test_seer_can_reveal_a_player_role(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $seerPlayerBuilder = $story->get(ClassicGameSetupedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $seerPlayerBuilder);
        $seerUserBuilder = $seerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $seerUserBuilder);
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfPlayerId = $werewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolfPlayerId);

        When::asUser($seerUserBuilder)->game()->seerReveal($werewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $response = When::asUser($seerUserBuilder)->player()->getCurrent();
        $this->assertSame(GameRoleEnum::SEER->value, $response->get('[role][type]'));
        $this->assertSame(GameRoleEnum::WEREWOLF->value, $response->get('[role][lastObservedRole]'));
        $this->assertSame($werewolfPlayerId->toString(), $response->get('[role][lastObservedPlayerId]'));
        $this->assertSame(
            GameRoleEnum::WEREWOLF->value,
            $response->get(\sprintf('[role][observedRoles][%s]', $werewolfPlayerId->toString())),
        );
    }

    public function test_anonymous_cannot_reveal(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfPlayerId = $werewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolfPlayerId);

        When::game()->seerReveal($werewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(401);
    }

    public function test_non_seer_cannot_reveal(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $villagerPlayerBuilder = $story->get(ClassicGameSetupedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $villagerPlayerBuilder);
        $villagerUserBuilder = $villagerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $villagerUserBuilder);
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfPlayerId = $werewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolfPlayerId);

        When::asUser($villagerUserBuilder)->game()->seerReveal($werewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_seer_cannot_reveal_self(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $seerPlayerBuilder = $story->get(ClassicGameSetupedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $seerPlayerBuilder);
        $seerUserBuilder = $seerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $seerUserBuilder);
        $seerPlayerId = $seerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($seerPlayerId);

        When::asUser($seerUserBuilder)->game()->seerReveal($seerPlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_seer_cannot_reveal_invalid_uuid(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $seerPlayerBuilder = $story->get(ClassicGameSetupedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $seerPlayerBuilder);
        $seerUserBuilder = $seerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $seerUserBuilder);

        When::asUser($seerUserBuilder)->game()->seerReveal('omg-i-do-not-exist');
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_seer_cannot_reveal_player_from_another_game(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $seerPlayerBuilder = $story->get(ClassicGameSetupedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $seerPlayerBuilder);
        $seerUserBuilder = $seerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $seerUserBuilder);

        $strangerPlayerBuilder = ThereIs::aPlayer()->build();
        $strangerPlayerId = $strangerPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($strangerPlayerId);

        When::asUser($seerUserBuilder)->game()->seerReveal($strangerPlayerId->toString());
        $this->assertResponseStatusCodeSame(404);
    }

    public function test_seer_cannot_reveal_twice_the_same_night(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $seerPlayerBuilder = $story->get(ClassicGameSetupedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $seerPlayerBuilder);
        $seerUserBuilder = $seerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $seerUserBuilder);
        $werewolf1PlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf1PlayerBuilder);
        $werewolf1PlayerId = $werewolf1PlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolf1PlayerId);
        $werewolf2PlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_2);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolf2PlayerBuilder);
        $werewolf2PlayerId = $werewolf2PlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolf2PlayerId);

        When::asUser($seerUserBuilder)->game()->seerReveal($werewolf1PlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        When::asUser($seerUserBuilder)->game()->seerReveal($werewolf2PlayerId->toString());
        $this->assertResponseStatusCodeSame(403);
    }

    public function test_time_up_moves_from_seer_turn_to_next_turn(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $seerPlayerBuilder = $story->get(ClassicGameSetupedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $seerPlayerBuilder);
        $seerUserBuilder = $seerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $seerUserBuilder);
        $gameBuilder = $story->get(ClassicGameSetupedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $clock->sleep(60);

        When::asUser($seerUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertSame(GameRuntimeStepEnum::NIGHT, $game->getRuntimeStep());
        $workflow = $game->getNightWorkflow();
        $this->assertNotNull($workflow);
        $this->assertContains(GameRoleEnum::WEREWOLF, $workflow->getCurrentTurn());
    }

    public function test_game_event_dispatched_by_seer_reveal(): void
    {
        $story = ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        $seerPlayerBuilder = $story->get(ClassicGameSetupedStory::SEER);
        $this->assertInstanceOf(PlayerBuilder::class, $seerPlayerBuilder);
        $seerUserBuilder = $seerPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $seerUserBuilder);
        $werewolfPlayerBuilder = $story->get(ClassicGameSetupedStory::WEREWOLF_1);
        $this->assertInstanceOf(PlayerBuilder::class, $werewolfPlayerBuilder);
        $werewolfPlayerId = $werewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($werewolfPlayerId);
        $gameBuilder = $story->get(ClassicGameSetupedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($seerUserBuilder)->game()->seerReveal($werewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $this->assertCollected(SeerRevealEvent::class, $seerUserBuilder->username, $gameBuilder->getEntity()->getId());
        $this->assertEventCollectedNumber(1);
    }
}
