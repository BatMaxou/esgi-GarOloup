<?php

namespace App\Fixtures;

use App\Fixtures\Story\ClassicGame\ClassicGameClosedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameConfiguredStory;
use App\Fixtures\Story\ClassicGame\ClassicGameDispatchedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameFilledStory;
use App\Fixtures\Story\ClassicGame\ClassicGameLaunchedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameNight1WerewolfVotedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameSetupedStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameClosedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameConfiguredWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameFilledWithGameMasterStory;
use App\Fixtures\Story\Game\GameCreatedStory;
use App\Tests\Helper\ThereIs;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ThereIs::anAdmin()->withEmail('admin@garoloup.com')->withUsername('Admin')->build();
        ThereIs::anUser()->withEmail('test@garoloup.com')->withUsername('Test')->build();
        ThereIs::anUser()->build(10);

        ThereIs::aStory(GameCreatedStory::class)->execute();
        ThereIs::aStory(ClassicGameFilledStory::class)->execute();
        ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        ThereIs::aStory(ClassicGameDispatchedStory::class)->execute();
        ThereIs::aStory(ClassicGameLaunchedStory::class)->execute();
        ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        ThereIs::aStory(ClassicGameNight1WerewolfVotedStory::class)->execute();

        ThereIs::aStory(ClassicGameFilledWithGameMasterStory::class)->execute();
        ThereIs::aStory(ClassicGameClosedWithGameMasterStory::class)->execute();
        ThereIs::aStory(ClassicGameConfiguredWithGameMasterStory::class)->execute();
    }
}
