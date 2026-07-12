import { useTranslations } from 'next-intl';

import Divider from '@/components/ui/atoms/divider';
import GlassPanel from '@/components/ui/atoms/glass-panel';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import { GameRuntimeStepEnum } from '@/utils/enums';
import type { PeriodRecap, PlayerRecap, BallotRecap } from '@/utils/types';
import { periodTypeIcon } from './config';
import EventCard from './event-card';
import { buildPeriodEvents } from './helpers';

type Props = {
  period: PeriodRecap;
  players: PlayerRecap[];
};

const PeriodSection = ({ period, players }: Props) => {
  const t = useTranslations();
  const events = buildPeriodEvents(period, players, t);
  if (events.length === 0) {
    return null;
  }

  const iconName = periodTypeIcon[period.type as keyof typeof periodTypeIcon];
  const periodKey = period.type.toLowerCase() as 'night' | 'day' | 'vote' | 'setup' | 'interrupt';
  const nameOf = (id?: string) => players.find((p) => p.playerId === id)?.username ?? '?';
  const showBallots = period.type === GameRuntimeStepEnum.VOTE && period.ballots.length > 0;

  return (
    <GlassPanel className="flex-col gap-3 p-5">
      <div className="flex items-center gap-2">
        {iconName && <Icon name={iconName} className="w-4 h-4 text-primary" />}
        <Typography variant="subtitle" bold textColor="text">
          {t(`components.pages.recap.period.${periodKey}`, { number: period.number })}
        </Typography>
      </div>
      {showBallots && (
        <>
          <div className="flex flex-col gap-2 pt-3">
            <Typography variant="body-xs" bold uppercase textColor="neutral-500" className="tracking-wide">
              {t('components.pages.recap.voteDetails')}
            </Typography>
            <ul className="flex flex-col gap-4">
              {period.ballots.map((ballot: BallotRecap, idx) => (
                <li key={idx}>
                  <Typography variant="body-sm" bold textColor="text" className="flex items-center gap-2">
                    {nameOf(ballot.playerId)}
                    <Icon name="arrow-right" />
                    {nameOf(ballot.targetPlayerId)}
                  </Typography>
                </li>
              ))}
            </ul>
          </div>
          <Divider variant="primary" />
        </>
      )}
      <div className="flex flex-col gap-2">
        {events.map((event) => (
          <EventCard key={event.id} event={event} />
        ))}
      </div>
    </GlassPanel>
  );
};

export default PeriodSection;
