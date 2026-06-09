'use client';

import { GameGlobalStepEnum } from '@/utils/enums';
import NewGameDisplay from './new-game-display';
import { useGame } from '@/contexts/game-context';

const RunningGameDisplay = ({ isHost, isGameMaster }: { isHost: boolean; isGameMaster: boolean }) => {
  const { game } = useGame();
  if (!game) {
    return null;
  }

  const globalStep = game.globalStep;

  if (globalStep === GameGlobalStepEnum.NEW) {
    return <NewGameDisplay game={game} isHost={isHost} isGameMaster={isGameMaster} />;
  }

  if (globalStep === GameGlobalStepEnum.RUNNING) {
    return <div>Running Game</div>;
  }
};

export default RunningGameDisplay;
