'use client';

import { useTranslations } from 'next-intl';

import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';
import Tabs from '@/components/ui/organisms/tabs';
import { GameTeamEnum } from '@/utils/enums';
import { paths } from '@/utils/paths';
import type { Recap } from '@/utils/types';
import { teamColorClass } from './config';
import JournalTab from './journal-tab';
import PlayersTab from './players-tab';

type Props = {
  recap: Recap;
};

const RecapClient = ({ recap }: Props) => {
  const t = useTranslations();
  const winningTeamColor = teamColorClass[recap.winningTeam] ?? 'text-primary';

  const tabs = [
    {
      label: t('components.pages.recap.tabs.players'),
      component: <PlayersTab recap={recap} />,
    },
    {
      label: t('components.pages.recap.tabs.journal'),
      component: <JournalTab recap={recap} />,
    },
  ];

  return (
    <main className="max-w-4xl mx-auto px-6 py-10 flex flex-col items-center gap-8">
      <div className="flex flex-col gap-2 items-center text-center">
        <Icon name="crown" className={`w-10 h-10 ${winningTeamColor}`} />
        <Typography variant="heading-2" bold textColor="text">
          {t('components.pages.recap.winnerTeam', {
            team:
              recap.winningTeam === GameTeamEnum.SOLO && recap.winningRole
                ? t(`components.pages.recap.role.${recap.winningRole}`)
                : t(`components.pages.recap.team.${recap.winningTeam}`),
          })}
        </Typography>
        {recap.winningRole && recap.winningTeam !== GameTeamEnum.SOLO && (
          <Typography variant="subtitle" textColor="neutral-400">
            {t(`components.pages.recap.role.${recap.winningRole}`)}
          </Typography>
        )}
      </div>

      <Tabs tabs={tabs} align="center" />

      <Button variant="accent" label={t('components.pages.recap.backToVillage')} href={paths.home} asLink popup />
    </main>
  );
};

export default RecapClient;
