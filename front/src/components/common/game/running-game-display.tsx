'use client';

import { GameGlobalStepEnum, GameRoleEnum, GameRuntimeStepEnum, GameTeamEnum } from '@/utils/enums';
import NewGameDisplay from './new-game-display';
import { useGame } from '@/contexts/game-context';
import WerewolfActions from './running-step/night-actions/werewolf-actions';
import { usePlayer } from '@/contexts/player-context';
import WitchActions from './running-step/night-actions/witch-actions';
import SeerActions from './running-step/night-actions/seer-actions';
import { UserTurnAnimation } from './animations/user-turn-animation';
import NightRecap from './sequencer/night-to-day';
import { useNightRecap } from './sequencer/night-to-day/use-night-recap';
import WaitingNightActions from './running-step/night-actions/waiting-night-actions';
import VoteDisplay from './running-step/vote/vote-display';
import HunterActions from './running-step/interrupt/hunter-actions';
import WaitingInterruptActions from './running-step/interrupt/waiting-interrupt-actions';
import WildChildActions from './running-step/setup/wild-child-actions';
import WaitingSetupActions from './running-step/setup/waiting-setup-actions';
import AssassinActions from './running-step/night-actions/assassin-action';
import CupidonActions from './running-step/setup/cupidon-action';

const RunningGameDisplay = ({ isHost, isGameMaster }: { isHost: boolean; isGameMaster: boolean }) => {
  const { game } = useGame();
  const { player } = usePlayer();
  const { isPlaying, currentBeat } = useNightRecap(game, player);

  const runningStep = game?.runtimeStep;
  const globalStep = game?.globalStep;
  const interruptedBy = game?.interruptedByRole;

  if (!game || !player) {
    return null;
  }

  if (globalStep === GameGlobalStepEnum.NEW) {
    return <NewGameDisplay game={game} isHost={isHost} isGameMaster={isGameMaster} />;
  }

  if (globalStep === GameGlobalStepEnum.RUNNING) {
    if (runningStep === GameRuntimeStepEnum.SETUP) {
      switch (true) {
        case player?.role?.type === GameRoleEnum.WILD_CHILD:
          return <WildChildActions />;
        case player?.role?.type === GameRoleEnum.CUPIDON:
          return <CupidonActions />;
        default:
          return <WaitingSetupActions />;
      }
    }
    if (runningStep === GameRuntimeStepEnum.NIGHT) {
      if (
        player.dead ||
        game?.players?.some((currentPlayer) => currentPlayer.id === player?.id && currentPlayer.dead)
      ) {
        return <WaitingNightActions />;
      }

      switch (true) {
        case player?.team === GameTeamEnum.WEREWOLF &&
          game.nightWorkflow?.currentTurn?.werewolf === GameRoleEnum.WEREWOLF:
          return (
            <>
              <UserTurnAnimation animateOnce />
              <WerewolfActions />
            </>
          );
        case player?.role?.type === GameRoleEnum.WITCH && game.nightWorkflow?.currentTurn?.witch === GameRoleEnum.WITCH:
          return (
            <>
              <UserTurnAnimation animateOnce />
              <WitchActions />
            </>
          );
        case player?.role?.type === GameRoleEnum.SEER && game.nightWorkflow?.currentTurn?.seer === GameRoleEnum.SEER:
          return (
            <>
              <UserTurnAnimation animateOnce />
              <SeerActions />
            </>
          );
        case player?.role?.type === GameRoleEnum.ASSASSIN &&
          game.nightWorkflow?.currentTurn?.assassin === GameRoleEnum.ASSASSIN:
          return (
            <>
              <UserTurnAnimation animateOnce />
              <AssassinActions />
            </>
          );
        default:
          return <WaitingNightActions />;
      }
    }

    if (runningStep === GameRuntimeStepEnum.DAY) {
      return <>{isPlaying && currentBeat && <NightRecap beat={currentBeat} />}</>;
    }

    if (runningStep === GameRuntimeStepEnum.VOTE) {
      return (
        <>
          {/* Transition avec affichage "Il est l'heure de voter" */}
          {/* Affichage du display de vote */}
          <VoteDisplay />
        </>
      );
    }

    if (runningStep === GameRuntimeStepEnum.INTERRUPT) {
      if (interruptedBy === player.role?.type) {
        switch (interruptedBy) {
          case GameRoleEnum.HUNTER:
            return <HunterActions />;
          default:
            return null;
        }
      }
      return <WaitingInterruptActions />;
    }
  }

  return null;
};

export default RunningGameDisplay;
