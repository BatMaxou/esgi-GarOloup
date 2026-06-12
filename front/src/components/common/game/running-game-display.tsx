'use client';

import { GameGlobalStepEnum, GameRoleEnum, GameRuntimeStepEnum } from '@/utils/enums';
import NewGameDisplay from './new-game-display';
import { useGame } from '@/contexts/game-context';
import WerewolfActions from './night-actions/werewolf-actions';
import { usePlayer } from '@/contexts/player-context';

const RunningGameDisplay = ({ isHost, isGameMaster }: { isHost: boolean; isGameMaster: boolean }) => {
  const { game } = useGame();
  const { player } = usePlayer();

  if (!game) {
    return null;
  }

  const globalStep = game.globalStep;
  const runningStep = game.runtimeStep;

  if (globalStep === GameGlobalStepEnum.NEW) {
    return <NewGameDisplay game={game} isHost={isHost} isGameMaster={isGameMaster} />;
  }

  if (globalStep === GameGlobalStepEnum.RUNNING) {
    if (runningStep === GameRuntimeStepEnum.NIGHT) {
      switch (true) {
        case player?.role?.type === GameRoleEnum.WEREWOLF && game.nightWorkflow?.currentTurn?.werewolf === GameRoleEnum.WEREWOLF:
          return <WerewolfActions />;
        default:
          return null;
      }
    }
  }
};

export default RunningGameDisplay;
