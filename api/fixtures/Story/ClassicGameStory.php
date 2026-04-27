<?php

namespace App\Fixtures\Story;

use App\Enum\Game\GameInitialisationStepEnum;
use App\Tests\Helper\ThereIs;
use Zenstruck\Foundry\Story;

final class ClassicGameStory extends Story
{
    public function build(): void
    {
        $roleBagBuilder = ThereIs::aRoleBag()->buildAll();
        $gameRoleBagBuilder = ThereIs::aGameRoleBag($roleBagBuilder)->build();

        $hostBuilder = ThereIs::anUser()->withEmail('classic-game-host@garoloup.com')->build();
        $hostPlayerBuilder = ThereIs::aPlayer()
            ->withUser($hostBuilder)
            ->withRole($gameRoleBagBuilder->getVillager())
            ->build();

        $temp1Builder = ThereIs::aTempUser()->withUsername('classic-game-temp1')->build();
        $temp1PlayerBuilder = ThereIs::aPlayer()
            ->withTempUser($temp1Builder)
            ->withRole($gameRoleBagBuilder->getWerewolf())
            ->build();
        $temp2Builder = ThereIs::aTempUser()->withUsername('classic-game-temp2')->build();
        $temp2PlayerBuilder = ThereIs::aPlayer()
            ->withTempUser($temp2Builder)
            ->withRole($gameRoleBagBuilder->getVillager())
            ->build();
        $temp3Builder = ThereIs::aTempUser()->withUsername('classic-game-temp3')->build();
        $temp3PlayerBuilder = ThereIs::aPlayer()
            ->withTempUser($temp3Builder)
            ->withRole($gameRoleBagBuilder->getVillager())
            ->build();

        $gameBuilder = ThereIs::aGame()
            ->withHost($hostPlayerBuilder)
            ->withPlayers([$hostPlayerBuilder, $temp1PlayerBuilder, $temp2PlayerBuilder, $temp3PlayerBuilder])
            ->withInitialisationStep(GameInitialisationStepEnum::FINISH)
            ->build();

        ThereIs::aConfiguration()
            ->forGame($gameBuilder)
            ->withComposition(ThereIs::aComposition()
                ->withRole($roleBagBuilder->getVillager(), 3)
                ->withRole($roleBagBuilder->getWerewolf(), 1)
                ->build())
            ->build();
    }
}
