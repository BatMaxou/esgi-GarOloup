'use client';

import { useTranslations } from 'next-intl';

import { useInfectFather } from '@/contexts/roles/infect-father-context';
import Card from '@/components/ui/molecules/card';
import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import { usePlayer } from '@/contexts/player-context';
import { useWerewolf } from '@/contexts/roles/werewolf-context';
import { getLastUsernameUserKilledDuringNight } from '@/utils/game';
import { useGame } from '@/contexts/game-context';
import Tag from '@/components/ui/molecules/tag';
import { useState } from 'react';

const InfectFatherActions = () => {
  const { player } = usePlayer();
  const { game } = useGame();
  const { infectionAvailable, infect, pass } = useInfectFather();
  const { team } = useWerewolf();
  const t = useTranslations('components.common.game.nightActions.infectFatherActions');
  const [passDisplay, setPassDisplay] = useState(false);

  const teammates = team?.members.filter((member) => member.id !== player?.id) ?? [];
  const lastUsernameUserKilled = game ? getLastUsernameUserKilledDuringNight(game, game?.players ?? []) : null;

  const handleSelectActionType = (actionType: 'infect' | 'pass') => {
    if (actionType === 'infect') {
      infect();
    } else if (actionType === 'pass') {
      setPassDisplay(true);
      pass();
    }
  };

  return (
    <div className="flex flex-col gap-8">
      <Card hoverable={false} orientation="horizontal" className="gap-6 items-center">
        <Icon name="werewolf" className="w-10 h-10" />
        <div className="flex flex-col gap-2">
          <Typography tag="h3" variant="subtitle" bold>
            {t('infectFather')}
          </Typography>
          <Typography tag="span" className="text-sm text-error!" bold>
            {t('werewolfCamp')}
          </Typography>
        </div>
      </Card>
      <Typography tag="p" variant="body">
        {t('roundDescription')}
      </Typography>
      {teammates.length > 0 && (
        <Card hoverable={false}>
          <Typography tag="span" className="phase-icon">
            {t('teamLabel')}
          </Typography>
          <div className="flex flex-row flex-wrap gap-x-6 gap-y-2">
            {teammates.map((member) => (
              <Typography key={member.id} variant="body" textColor="light" className="flex items-center gap-2">
                <Icon name="werewolf" className="w-4 h-4 color-error" />
                {member.username}
              </Typography>
            ))}
          </div>
        </Card>
      )}
      {!passDisplay ? (
        <div className="flex w-full flex-col gap-2 sm:flex-row sm:items-stretch">
          <Card
            liftOnHover={false}
            className="flex w-full flex-1 min-w-0 flex-col gap-2 items-center cursor-pointer border-error/40!"
            onClick={() => handleSelectActionType('infect')}
          >
            <Icon name="infect" className="w-10 h-10 shrink-0 text-error/60" />
            <Typography tag="p" variant="body" className="font-bold" center>
              {t('infectTitle')}
            </Typography>
            <Typography tag="span" variant="body-sm" className="w-full flex-1 text-primary/60" center>
              {t('infectDescription', { username: lastUsernameUserKilled ?? '' })}
            </Typography>
            <Tag
              label={infectionAvailable ? t('infectTag') : t('infectTagDisabled')}
              variant={infectionAvailable ? 'error' : 'neutral'}
              disabled={!infectionAvailable}
              size="lg"
            />
          </Card>
          <Card
            liftOnHover={false}
            className="flex w-full flex-1 min-w-0 flex-col gap-2 items-center cursor-pointer"
            onClick={() => handleSelectActionType('pass')}
          >
            <Icon name="hand" className="w-10 h-10 shrink-0 text-neutral/60" />
            <Typography tag="span" variant="body" className="font-bold" center>
              {t('passTitle')}
            </Typography>
            <Typography tag="span" variant="body-sm" className="w-full flex-1 text-primary/60" center>
              {t('passDescription')}
            </Typography>
            <span className="invisible h-9 shrink-0" aria-hidden />
          </Card>
        </div>
      ) : (
        <div className="flex flex-col gap-2 mt-8 items-center justify-center">
          <Typography tag="p" variant="subtitle" bold>
            {t('passEnded')}
          </Typography>
        </div>
      )}
    </div>
  );
};

export default InfectFatherActions;
