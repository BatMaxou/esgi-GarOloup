<?php

namespace App\Fixtures;

use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameClosedStory;
use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameConfiguredStory;
use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameDispatchedStory;
use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameFilledStory;
use App\Fixtures\Story\ClassicGame\Initialisation\ClassicGameLaunchedStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\ClassicGameClosedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\ClassicGameConfiguredWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\ClassicGameDispatchedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\ClassicGameFilledWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\ClassicGameLaunchedWithGameMasterStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\ClassicGameWithGameMasterSettedStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\RandomDispatch\ClassicGameConfiguredWithGameMasterAndRandomDispatchStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\RandomDispatch\ClassicGameDispatchedWithGameMasterAndRandomDispatchStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\RandomDispatch\ClassicGameLaunchedWithGameMasterAndRandomDispatchStory;
use App\Fixtures\Story\ClassicGame\Initialisation\GameMaster\RandomDispatch\ClassicGameWithGameMasterSettedAndRandomDispatchStory;
use App\Fixtures\Story\ClassicGame\Runtime\Day\ClassicGameDay1FinishedStory;
use App\Fixtures\Story\ClassicGame\Runtime\Day\ClassicGameDay2FinishedStory;
use App\Fixtures\Story\ClassicGame\Runtime\Night\Seer\ClassicGameNight1SeerRevealedStory;
use App\Fixtures\Story\ClassicGame\Runtime\Night\Seer\ClassicGameNight2SeerRevealedStory;
use App\Fixtures\Story\ClassicGame\Runtime\Night\Werewolf\ClassicGameNight1WerewolfVotedStory;
use App\Fixtures\Story\ClassicGame\Runtime\Night\Werewolf\ClassicGameNight2WerewolfVotedStory;
use App\Fixtures\Story\ClassicGame\Runtime\Setup\ClassicGameSetupedStory;
use App\Fixtures\Story\ClassicGame\Runtime\Vote\ClassicGameVote1ResolvedStory;
use App\Fixtures\Story\ComplexGame\Initialisation\ComplexGameClosedStory;
use App\Fixtures\Story\ComplexGame\Initialisation\ComplexGameConfiguredStory;
use App\Fixtures\Story\ComplexGame\Initialisation\ComplexGameDispatchedStory;
use App\Fixtures\Story\ComplexGame\Initialisation\ComplexGameFilledStory;
use App\Fixtures\Story\ComplexGame\Initialisation\ComplexGameLaunchedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Day\ComplexGameDay1FinishedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Day\ComplexGameDay2FinishedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Hunter\ComplexGameNight1HunterInterruptStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Hunter\ComplexGameNight1WerewolfKilledHunterStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Seer\ComplexGameNight1SeerRevealedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Seer\ComplexGameNight2SeerRevealedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Seer\ComplexGameNight3SeerRevealedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf\ComplexGameNight1WerewolfVotedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf\ComplexGameNight2WerewolfVotedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Werewolf\ComplexGameNight3WerewolfVotedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Witch\ComplexGameNight1WitchSavedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Witch\ComplexGameNight2WitchPassedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Night\Witch\ComplexGameNight3WitchPoisonedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Setup\ComplexGameSetupedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Vote\ComplexGameVote1HunterEliminatedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Vote\ComplexGameVote1HunterInterruptStory;
use App\Fixtures\Story\ComplexGame\Runtime\Vote\ComplexGameVote1ResolvedStory;
use App\Fixtures\Story\ComplexGame\Runtime\Vote\ComplexGameVote2ResolvedStory;
use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinNight3PoisonedStory;
use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinNight3Story;
use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinNight4Story;
use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinNight5Story;
use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinVote3Story;
use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinVote4Story;
use App\Fixtures\Story\ComplexGame\Runtime\WerewolfWin\ComplexGameWerewolfWinVote5Story;
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

        // Classic Game ---------------------------
        ThereIs::aStory(ClassicGameFilledStory::class)->execute();
        ThereIs::aStory(ClassicGameClosedStory::class)->execute();
        ThereIs::aStory(ClassicGameConfiguredStory::class)->execute();
        ThereIs::aStory(ClassicGameDispatchedStory::class)->execute();
        ThereIs::aStory(ClassicGameLaunchedStory::class)->execute();
        // ThereIs::aStory(ClassicGameSetupedStory::class)->execute();
        // ThereIs::aStory(ClassicGameNight1SeerRevealedStory::class)->execute();
        // ThereIs::aStory(ClassicGameNight1WerewolfVotedStory::class)->execute();
        // ThereIs::aStory(ClassicGameDay1FinishedStory::class)->execute();
        // ThereIs::aStory(ClassicGameVote1ResolvedStory::class)->execute();
        // ThereIs::aStory(ClassicGameNight2SeerRevealedStory::class)->execute();
        // ThereIs::aStory(ClassicGameNight2WerewolfVotedStory::class)->execute();
        // ThereIs::aStory(ClassicGameDay2FinishedStory::class)->execute();

        // // Game Master ---------------------------
        // ThereIs::aStory(ClassicGameFilledWithGameMasterStory::class)->execute();
        // ThereIs::aStory(ClassicGameClosedWithGameMasterStory::class)->execute();
        // ThereIs::aStory(ClassicGameConfiguredWithGameMasterStory::class)->execute();
        // ThereIs::aStory(ClassicGameWithGameMasterSettedStory::class)->execute();
        // ThereIs::aStory(ClassicGameDispatchedWithGameMasterStory::class)->execute();
        // ThereIs::aStory(ClassicGameLaunchedWithGameMasterStory::class)->execute();
        //
        // // Random Dispatch ---------------------------
        // ThereIs::aStory(ClassicGameConfiguredWithGameMasterAndRandomDispatchStory::class)->execute();
        // ThereIs::aStory(ClassicGameWithGameMasterSettedAndRandomDispatchStory::class)->execute();
        // ThereIs::aStory(ClassicGameDispatchedWithGameMasterAndRandomDispatchStory::class)->execute();
        // ThereIs::aStory(ClassicGameLaunchedWithGameMasterAndRandomDispatchStory::class)->execute();
        //
        // // Complex Game ----------------------------
        // ThereIs::aStory(ComplexGameFilledStory::class)->execute();
        // ThereIs::aStory(ComplexGameClosedStory::class)->execute();
        // ThereIs::aStory(ComplexGameConfiguredStory::class)->execute();
        // ThereIs::aStory(ComplexGameDispatchedStory::class)->execute();
        // ThereIs::aStory(ComplexGameLaunchedStory::class)->execute();
        // ThereIs::aStory(ComplexGameSetupedStory::class)->execute();
        // ThereIs::aStory(ComplexGameNight1SeerRevealedStory::class)->execute();
        // ThereIs::aStory(ComplexGameNight1WerewolfVotedStory::class)->execute();
        // ThereIs::aStory(ComplexGameNight1WitchSavedStory::class)->execute();
        // ThereIs::aStory(ComplexGameDay1FinishedStory::class)->execute();
        // ThereIs::aStory(ComplexGameVote1ResolvedStory::class)->execute();
        // ThereIs::aStory(ComplexGameNight2SeerRevealedStory::class)->execute();
        // ThereIs::aStory(ComplexGameNight2WerewolfVotedStory::class)->execute();
        // ThereIs::aStory(ComplexGameNight2WitchPassedStory::class)->execute();
        // ThereIs::aStory(ComplexGameDay2FinishedStory::class)->execute();
        // ThereIs::aStory(ComplexGameVote2ResolvedStory::class)->execute();
        // ThereIs::aStory(ComplexGameNight3SeerRevealedStory::class)->execute();
        // ThereIs::aStory(ComplexGameNight3WerewolfVotedStory::class)->execute();
        // ThereIs::aStory(ComplexGameNight3WitchPoisonedStory::class)->execute();
        //
        // // Hunter ----------------------------------
        // ThereIs::aStory(ComplexGameNight1WerewolfKilledHunterStory::class)->execute();
        // ThereIs::aStory(ComplexGameNight1HunterInterruptStory::class)->execute();
        // ThereIs::aStory(ComplexGameVote1HunterEliminatedStory::class)->execute();
        // ThereIs::aStory(ComplexGameVote1HunterInterruptStory::class)->execute();
        //
        // // Werewolf Win ----------------------------
        // ThereIs::aStory(ComplexGameWerewolfWinNight3Story::class)->execute();
        // ThereIs::aStory(ComplexGameWerewolfWinNight3PoisonedStory::class)->execute();
        // ThereIs::aStory(ComplexGameWerewolfWinNight4Story::class)->execute();
        // ThereIs::aStory(ComplexGameWerewolfWinNight5Story::class)->execute();
        // ThereIs::aStory(ComplexGameWerewolfWinVote3Story::class)->execute();
        // ThereIs::aStory(ComplexGameWerewolfWinVote4Story::class)->execute();
        // ThereIs::aStory(ComplexGameWerewolfWinVote5Story::class)->execute();
    }
}
