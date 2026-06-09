import { useMemo } from 'react';
import { useTranslations } from 'next-intl';
import { useGame } from '@/contexts/game-context';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Divider from '@/components/ui/atoms/divider';
import Button from '@/components/ui/molecules/button';

const PreLaunchGameDisplay = () => {
  const t = useTranslations('components.common.game.new-game-display');
  const { game, launchGame } = useGame();

  const gameMasterUsername = game?.gameMaster?.username ?? game?.gameMaster?.user?.username ?? '-';
  const playersCount = game?.players?.length ?? 0;

  return (
    <Card className="w-full px-20! py-10!" orientation="vertical" hoverable={false}>
      <div className="flex flex-col gap-4 w-full">
        <div className="flex flex-col gap-1">
          <Typography variant="subtitle" bold textColor="light" className="text-center">
            {t('summaryTitle')}
          </Typography>
          <Typography variant="body" textColor="secondary" className="text-center">
            {t('summarySubtitle')}
          </Typography>
        </div>

        <Divider variant="primary" />

        <Typography variant="body" textColor="light">
          {t('summaryGameMaster', { username: gameMasterUsername })}
        </Typography>

        <Divider variant="primary" />

        <Typography variant="body" textColor="light">
          {t('summaryPlayersCount', { count: playersCount })}
        </Typography>

        <Divider variant="primary" />

        <Button variant="accent" className="w-full" label={t('launchGameButton')} onClick={launchGame} />
      </div>
    </Card>
  );
};

export default PreLaunchGameDisplay;
