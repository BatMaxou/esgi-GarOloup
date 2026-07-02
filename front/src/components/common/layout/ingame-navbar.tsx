'use client';

import { useTranslations } from 'next-intl';

import { Link } from '@/i18n/navigation';
import { paths } from '@/utils/paths';
import Typography from '@/components/ui/atoms/typography';
import Icon from '@/components/ui/atoms/icon';
import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';
import { useApiClient } from '@/contexts/api-context';
import Timer from '@/components/ui/molecules/timer';

import GameJoinCode from '../game/game-join-code';
import { headerBorderClasses, headerSurfaceClasses } from './header-surface';
import GameStepIndicator from '../game/game-step-indicator';
import { GameGlobalStepEnum } from '@/utils/enums';

const IngameNavbar = () => {
  const { game, timeUp } = useGame();
  const { player } = usePlayer();
  const { isTokenInitialized } = useApiClient();
  const tg = useTranslations();

  const isPlayerDead = game?.players?.some((currentPlayer) => currentPlayer.id === player?.id && currentPlayer.dead);
  const rightSideContent = () => {
    switch (game?.globalStep) {
      case GameGlobalStepEnum.NEW:
        return <GameJoinCode joinCode={game?.joinCode ?? ''} />;
      case GameGlobalStepEnum.RUNNING:
        return (
          <div className="flex items-center gap-4">
            {isPlayerDead && (
              <div className="flex items-center gap-2 rounded-sm border border-error/40 bg-error/10 px-3 py-1">
                <Icon name="skull" className="w-4 h-4 text-error" />
                <Typography tag="span" variant="body-sm" className="text-error!" bold>
                  {tg('components.common.layout.ingameNavbar.dead')}
                </Typography>
              </div>
            )}
            {game?.stepEndAt && isTokenInitialized ? <Timer stepEndAt={game.stepEndAt} onTimeOut={timeUp} /> : null}
          </div>
        );
      case GameGlobalStepEnum.FINISH:
        return null;
      default:
        return null;
    }
  };

  return (
    <nav
      className={`border-b ${headerBorderClasses} px-4 py-2 flex shrink-0 items-center justify-between sticky top-0 z-24 w-full ${headerSurfaceClasses}`}
    >
      <Link href={paths.home} className="flex items-center">
        <Typography tag="h1" variant="subtitle" textColor="accent" special className="text-glow-accent">
          {tg('name')}
        </Typography>
      </Link>

      <GameStepIndicator />

      {/* Nombre de secondes restantes */}
      {/* <GameTimerDisplay /> */}

      {/* Join code avec copy */}
      {rightSideContent()}
    </nav>
  );
};

export default IngameNavbar;
