<?php

namespace App\Tests\Functional\Game\Runtime;

use App\Enum\Game\GameRuntimeStepEnum;
use App\Fixtures\Story\ComplexGame\Runtime\Assassin\ComplexGameAssassinNight1ResolvedStory;
use App\Tests\GarOloupApiTestCase;
use App\Tests\Helper\Builder\Game\GameBuilder;
use App\Tests\Helper\Builder\Game\PlayerBuilder;
use App\Tests\Helper\ThereIs;

class AssassinTurnRuleTest extends GarOloupApiTestCase
{
    public function test_assassin_turn_is_skipped_on_the_first_night(): void
    {
        $story = ThereIs::aStory(ComplexGameAssassinNight1ResolvedStory::class)->execute();
        $assassinPlayerBuilder = $story->get(ComplexGameAssassinNight1ResolvedStory::ASSASSIN);
        $this->assertInstanceOf(PlayerBuilder::class, $assassinPlayerBuilder);
        $gameBuilder = $story->get(ComplexGameAssassinNight1ResolvedStory::GAME);
        $this->assertInstanceOf(GameBuilder::class, $gameBuilder);

        // La nuit 1 se résout jusqu'au jour : preuve que le tour assassin (présent dans
        // le workflow) a bien été sauté au lieu de bloquer la nuit.
        $this->assertSame(GameRuntimeStepEnum::DAY, $gameBuilder->getEntity()->getRuntimeStep());
        $this->assertFalse($assassinPlayerBuilder->getEntity()->isDead());
    }
}
