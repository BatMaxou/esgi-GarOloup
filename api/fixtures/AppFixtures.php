<?php

namespace App\Fixtures;

use App\Fixtures\Story\ClassicGame\ClassicGameClosedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameConfiguredStory;
use App\Fixtures\Story\ClassicGame\ClassicGameDay1FinishedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameDay2FinishedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameDispatchedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameFilledStory;
use App\Fixtures\Story\ClassicGame\ClassicGameLaunchedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameNight1SeerRevealedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameNight1WerewolfVotedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameNight2SeerRevealedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameNight2WerewolfVotedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameSetupedStory;
use App\Fixtures\Story\ClassicGame\ClassicGameVote1ResolvedStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameClosedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameConfiguredWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameDispatchedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameFilledWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameLaunchedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\GameMaster\ClassicGameWithGameMasterSettedStory;
use App\Fixtures\Story\ClassicGame\GameMaster\RandomDispatch\ClassicGameConfiguredWithGameMasterAndRandomDispatchStory;
use App\Fixtures\Story\ClassicGame\GameMaster\RandomDispatch\ClassicGameDispatchedWithGameMasterAndRandomDispatchStory;
use App\Fixtures\Story\ClassicGame\GameMaster\RandomDispatch\ClassicGameLaunchedWithGameMasterAndRandomDispatchStory;
use App\Fixtures\Story\ClassicGame\GameMaster\RandomDispatch\ClassicGameWithGameMasterSettedAndRandomDispatchStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameClosedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameConfiguredStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameDay1FinishedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameDay2FinishedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameDispatchedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameFilledStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameLaunchedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight1SeerRevealedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight1WerewolfVotedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight1WitchSavedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight2SeerRevealedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight2WerewolfVotedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight2WitchPassedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight3SeerRevealedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight3WerewolfVotedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameNight3WitchPoisonedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameSetupedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameVote1ResolvedStory;
use App\Fixtures\Story\ClassicWitchGame\ClassicWitchGameVote2ResolvedStory;
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

        // Classic Game ---------------------------        ThereIs::aStory(ClassicGameFilledStory::class)->execute();
        ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        ThereIs::aStory(ClassicGameDispatchedStory::class)->execute();
        ThereIs::aStory(ClassicGameLaunchedStory::class)->execute();
        ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        ThereIs::aStory(ClassicGameNight1SeerRevealedStory::class)->execute();
        ThereIs::aStory(ClassicGameNight1WerewolfVotedStory::class)->execute();
        ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        ThereIs::aStory(ClassicGameVote1ResolvedStory::class)->execute();
        ThereIs::aStory(ClassicGameNight2SeerRevealedStory::class)->execute();
        ThereIs::aStory(ClassicGameNight2WerewolfVotedStory::class)->execute();
        ThereIs::aStory(ClassicGameDay2FinishedStory::class)->execute();

        // Game Master
        ThereIs::aStory(ClassicGameFilledWithGameMasterStory::class)->execute();
        ThereIs::aStory(ClassicGameClosedWithGameMasterStory::class)->execute();
        ThereIs::aStory(ClassicGameConfiguredWithGameMasterStory::class)->execute();
        ThereIs::aStory(ClassicGameWithGameMasterSettedStory::class)->execute();
        ThereIs::aStory(ClassicGameDispatchedWithGameMasterStory::class)->execute();
        ThereIs::aStory(ClassicGameLaunchedWithGameMasterStory::class)->execute();

        // Random Dispatch
        ThereIs::aStory(ClassicGameConfiguredWithGameMasterAndRandomDispatchStory::class)->execute();
        ThereIs::aStory(ClassicGameWithGameMasterSettedAndRandomDispatchStory::class)->execute();
        ThereIs::aStory(ClassicGameDispatchedWithGameMasterAndRandomDispatchStory::class)->execute();
        ThereIs::aStory(ClassicGameLaunchedWithGameMasterAndRandomDispatchStory::class)->execute();

        // Witch Game ----------------------------
        ThereIs::aStory(ClassicWitchGameFilledStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameClosedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameConfiguredStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameDispatchedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameLaunchedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameSetupedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameNight1SeerRevealedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameNight1WerewolfVotedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameNight1WitchSavedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameDay1FinishedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameVote1ResolvedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameNight2SeerRevealedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameNight2WerewolfVotedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameNight2WitchPassedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameDay2FinishedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameVote2ResolvedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameNight3SeerRevealedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameNight3WerewolfVotedStory::class)->execute();
        ThereIs::aStory(ClassicWitchGameNight3WitchPoisonedStory::class)->execute();
    }
}
