'use client';

import JoinGame from '@/components/common/lobby/joinGame';
import PublicGames from '@/components/common/lobby/publicGames';
import CreateGame from '@/components/common/lobby/createGame';
import Card from '@/components/ui/molecules/card';
import Button from '@/components/ui/molecules/button';
import Tabs from '@/components/ui/organisms/tabs';
import Typography from '@/components/ui/atoms/typography';
import { useTranslations } from 'next-intl';
import { paths } from '@/utils/paths';
import { usePlayer } from '@/contexts/player-context';

const LobbyClient = () => {
  const t = useTranslations('components.pages.lobby');
  const { player } = usePlayer();

  const joinCode = player?.game?.joinCode;

  return (
    <main className="flex w-full flex-col items-center px-2 py-8 xs:px-8 sm:px-16 sm:py-16 md:px-32 md:py-32">
      {player && (
        <Card variant="accent" liftOnHover={false} className="mb-8 w-full max-w-2xl px-5 py-5 sm:px-6 sm:py-6">
          <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div className="flex min-w-0 flex-1 flex-col gap-2">
              <Typography tag="h2" variant="subtitle" bold textColor="light">
                {t('inGameTitle')}
              </Typography>
              <Typography tag="p" variant="body-sm" textColor="neutral-200" className="leading-relaxed">
                {t('inGameDescription')}
              </Typography>
              {joinCode && (
                <Typography tag="p" variant="body-xs" bold uppercase textColor="neutral-300" className="tracking-wide">
                  {t('inGameJoinCode', { code: joinCode })}
                </Typography>
              )}
            </div>
            <Button variant="neutral" size="sm" label={t('resumeGame')} asLink href={paths.game} glass className="shrink-0" />
          </div>
        </Card>
      )}
      <Tabs
        tabs={[
          { label: t('public'), component: <PublicGames /> },
          { label: t('join'), component: <JoinGame /> },
          { label: t('create'), component: <CreateGame /> },
        ]}
        align="center"
      />
    </main>
  );
};

export default LobbyClient;
