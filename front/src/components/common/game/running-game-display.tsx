'use client';

import { GameGlobalStepEnum, GameRoleEnum, GameRuntimeStepEnum } from '@/utils/enums';
import NewGameDisplay from './new-game-display';
import { useGame } from '@/contexts/game-context';
import WerewolfActions from './night-actions/werewolf-actions';
import { usePlayer } from '@/contexts/player-context';
import WitchActions from './night-actions/witch-actions';
import SeerActions from './night-actions/seer-actions';
import { UserTurnAnimation } from './animations/user-turn-animation';
import NightRecap from './sequencer/night-to-day';
import { useNightRecap } from './sequencer/night-to-day/use-night-recap';
import WaitingNightActions from './night-actions/waiting-night-actions';

const RunningGameDisplay = ({ isHost, isGameMaster }: { isHost: boolean; isGameMaster: boolean }) => {
  const { game } = useGame();
  const { player } = usePlayer();
  const { isPlaying, currentBeat } = useNightRecap(game, player);

  const runningStep = game?.runtimeStep;
  const globalStep = game?.globalStep;

  if (!game || !player) {
    return null;
  }

  if (globalStep === GameGlobalStepEnum.NEW) {
    return <NewGameDisplay game={game} isHost={isHost} isGameMaster={isGameMaster} />;
  }

  if (globalStep === GameGlobalStepEnum.RUNNING) {
    if (runningStep === GameRuntimeStepEnum.NIGHT) {
      switch (true) {
        case player?.role?.type === GameRoleEnum.WEREWOLF &&
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
        default:
          return <WaitingNightActions />;
      }
    }

    if (runningStep === GameRuntimeStepEnum.DAY) {
      return <>
        {isPlaying && currentBeat && <NightRecap beat={currentBeat} />}
      </>;
    }
  }
};

export default RunningGameDisplay;
