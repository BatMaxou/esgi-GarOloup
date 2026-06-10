'use client';

import { useTranslations } from 'next-intl';

import { Link } from '@/i18n/navigation';
import { paths } from '@/utils/paths';
import Typography from '@/components/ui/atoms/typography';
import { useGame } from '@/contexts/game-context';

import GameJoinCode from '../game/game-join-code';
import { headerBorderClasses, headerSurfaceClasses } from './header-surface';
import GameStepIndicator from '../game/game-step-indicator';

const IngameNavbar = () => {
  const { game } = useGame();
  const tg = useTranslations();

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
      <GameJoinCode joinCode={game?.joinCode ?? ''} />
    </nav>
  );
};

export default IngameNavbar;
