'use client';

import { GameGlobalStepEnum, GameRoleEnum, GameRuntimeStepEnum } from '@/utils/enums';
import NewGameDisplay from './new-game-display';
import { useGame } from '@/contexts/game-context';
import WerewolfActions from './night-actions/werewolf-actions';
import { usePlayer } from '@/contexts/player-context';
import WitchActions from './night-actions/witch-actions';
import Typography from '@/components/ui/atoms/typography';

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
        case player?.role?.type === GameRoleEnum.WEREWOLF &&
          game.nightWorkflow?.currentTurn?.werewolf === GameRoleEnum.WEREWOLF:
          return <WerewolfActions />;
        case player?.role?.type === GameRoleEnum.WITCH && game.nightWorkflow?.currentTurn?.witch === GameRoleEnum.WITCH:
          return <WitchActions />;
        case player?.role?.type === GameRoleEnum.SEER && game.nightWorkflow?.currentTurn?.seer === GameRoleEnum.SEER:
          return (
            <Typography tag="p" variant="body" className="text-primary/60">
              {'je suis la seer'}
            </Typography>
          );
        default:
          return null;
      }
    }
  }
};

export default RunningGameDisplay;
