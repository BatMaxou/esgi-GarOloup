'use client';

import { Game } from '@/utils/types';
import { GameGlobalStepEnum } from '@/utils/enums';
import NewGameDisplay from './new-game-display';

const RunningGameDisplay = ({ game, isHost }: { game: Game; isHost: boolean }) => {
  if (!game) {
    return null;
  }

  const globalStep = game.globalStep;

  if (globalStep === GameGlobalStepEnum.NEW) {
    return <NewGameDisplay game={game} isHost={isHost} />;
  }

  if (globalStep === GameGlobalStepEnum.RUNNING) {
    return <div>Running Game</div>;
  }
};

export default RunningGameDisplay;
