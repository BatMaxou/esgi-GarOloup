'use client';

import { useMemo } from 'react';
import { useTranslations } from 'next-intl';
import { cva } from 'class-variance-authority';

import { Link, usePathname } from '@/i18n/navigation';
import { paths } from '@/utils/paths';
import Typography from '@/components/ui/atoms/typography';
import { useGame } from '@/contexts/game-context';

import GameJoinCode from '../game/game-join-code';

const navbarCva = cva(
  'border-b border-primary px-4 py-2 flex items-center justify-between fixed top-0 left-0 right-0 backdrop-blur-sm z-24',
  {
    variants: {
      sticky: {
        true: 'sticky',
        false: 'fixed',
      },
    },
  }
);

const IngameNavbar = () => {
  const { game } = useGame();
  const pathname = usePathname();
  const isSticky = useMemo(() => pathname !== paths.home, [pathname]);
  const tg = useTranslations();

  return (
    <nav className={navbarCva({ sticky: isSticky })}>
      <Link href={paths.home} className="flex items-center">
        <Typography tag="h1" variant="subtitle" textColor="accent" special className="text-glow-accent">
          {tg('name')}
        </Typography>
      </Link>

      {/* Indiquateur Jour/Nuit + n° manche */}
      {/* <GameStepIndicator /> */}

      {/* Nombre de secondes restantes */}
      {/* <GameTimerDisplay /> */}

      {/* Join code avec copy */}
      <GameJoinCode joinCode={game?.joinCode ?? ''} />
    </nav>
  );
};

export default IngameNavbar;
