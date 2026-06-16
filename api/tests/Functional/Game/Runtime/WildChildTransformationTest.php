<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Entity\Game\Role\WildChildRole;
use App\Enum\Game\GameRuntimeStepEnum;
use App\Enum\Game\GameTeamEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf\ComplexGameNight3WerewolfVotedStory;
use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinNight3Story;
use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinVote5Story;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\Builder\User\UserBuilder;
use App\Tests\Helper\ThereIs;
use App\Tests\Helper\When;
use Symfony\Component\Clock\Test\ClockSensitiveTrait;

class WildChildTransformationTest extends GarOloupApiTestCase
{
    use ClockSensitiveTrait;

    public function test_wild_child_wins_with_the_village_when_its_model_survives(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameNight3WerewolfVotedStory::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameNight3WerewolfVotedStory::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $lastWerewolfPlayerBuilder = $story->get(ComplexGameNight3WerewolfVotedStory::WEREWOLF_3);
        $this->assertInstanceOf(PlayerBuilder::class, $lastWerewolfPlayerBuilder);
        $lastWerewolfPlayerId = $lastWerewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($lastWerewolfPlayerId);
        $modelPlayerBuilder = $story->get(ComplexGameNight3WerewolfVotedStory::VILLAGER_1);
        $this->assertInstanceOf(PlayerBuilder::class, $modelPlayerBuilder);
        $wildChildPlayerBuilder = $story->get(ComplexGameNight3WerewolfVotedStory::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $gameBuilder = $story->get(ComplexGameNight3WerewolfVotedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($witchUserBuilder)->game()->witchPoison($lastWerewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();
        $this->assertFalse($modelPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameTeamEnum::VILLAGE, $wildChildPlayerBuilder->getEntity()->getTeam());

        $wildChildRole = $wildChildPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(WildChildRole::class, $wildChildRole);
        $this->assertFalse($wildChildRole->isTransformed());
        $this->assertSame(GameTeamEnum::VILLAGE, $game->getWinningTeam());
        $this->assertSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }

    public function test_wild_child_transforms_and_game_continues_when_its_model_the_last_werewolf_is_poisoned(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameWerewolfWinNight3Story::class)->execute();
        $witchPlayerBuilder = $story->get(ComplexGameWerewolfWinNight3Story::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $witchUserBuilder = $witchPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $witchUserBuilder);
        $lastWerewolfPlayerBuilder = $story->get(ComplexGameWerewolfWinNight3Story::WEREWOLF_3);
        $this->assertInstanceOf(PlayerBuilder::class, $lastWerewolfPlayerBuilder);
        $lastWerewolfPlayerId = $lastWerewolfPlayerBuilder->getEntity()->getId();
        $this->assertNotNull($lastWerewolfPlayerId);
        $wildChildPlayerBuilder = $story->get(ComplexGameWerewolfWinNight3Story::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $gameBuilder = $story->get(ComplexGameWerewolfWinNight3Story::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        When::asUser($witchUserBuilder)->game()->witchPoison($lastWerewolfPlayerId->toString());
        $this->assertResponseStatusCodeSame(200);

        $clock->sleep(60);

        When::game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();

        $this->assertTrue($lastWerewolfPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameTeamEnum::WEREWOLF, $wildChildPlayerBuilder->getEntity()->getTeam());

        $wildChildRole = $wildChildPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(WildChildRole::class, $wildChildRole);
        $this->assertTrue($wildChildRole->isTransformed());
        $this->assertNull($game->getWinningTeam());
        $this->assertNotSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }

    public function test_wild_child_wins_with_the_werewolves_through_the_remontada(): void
    {
        $clock = static::mockTime();

        $story = ThereIs::aStory(ComplexGameWerewolfWinVote5Story::class)->execute();
        $wildChildPlayerBuilder = $story->get(ComplexGameWerewolfWinVote5Story::WILD_CHILD);
        $this->assertInstanceOf(PlayerBuilder::class, $wildChildPlayerBuilder);
        $wildChildUserBuilder = $wildChildPlayerBuilder->user;
        $this->assertInstanceOf(UserBuilder::class, $wildChildUserBuilder);
        $witchPlayerBuilder = $story->get(ComplexGameWerewolfWinVote5Story::WITCH);
        $this->assertInstanceOf(PlayerBuilder::class, $witchPlayerBuilder);
        $gameBuilder = $story->get(ComplexGameWerewolfWinVote5Story::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        $clock->sleep(60);

        When::asUser($wildChildUserBuilder)->game()->timeUp();
        $this->assertResponseStatusCodeSame(200);

        $game = $gameBuilder->getEntity();

        $this->assertTrue($witchPlayerBuilder->getEntity()->isDead());
        $this->assertSame(GameTeamEnum::WEREWOLF, $wildChildPlayerBuilder->getEntity()->getTeam());

        $wildChildRole = $wildChildPlayerBuilder->getEntity()->getRole();
        $this->assertInstanceOf(WildChildRole::class, $wildChildRole);
        $this->assertTrue($wildChildRole->isTransformed());
        $this->assertSame(GameTeamEnum::WEREWOLF, $game->getWinningTeam());
        $this->assertSame(GameRuntimeStepEnum::FINISH, $game->getRuntimeStep());
    }
}
