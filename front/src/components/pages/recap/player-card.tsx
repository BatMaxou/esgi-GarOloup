import { useTranslations } from 'next-intl';

import Icon from '@/components/ui/atoms/icon';
import Typography from '@/components/ui/atoms/typography';
import type { PlayerRecap } from '@/utils/types';
import { roleIcon, teamCardClasses } from './config';
import { recapPlayerTeam } from './helpers';

type Props = {
  player: PlayerRecap;
  bothLoversAlive: boolean;
};

const PlayerCard = ({ player, bothLoversAlive }: Props) => {
  const t = useTranslations();
  const team = recapPlayerTeam(player, bothLoversAlive);
  const colorClass = team ? teamCardClasses[team] : 'border-primary/15';

  return (
    <div
      className={`relative flex min-w-[120px] flex-col items-center gap-2 overflow-hidden rounded-sm border bg-[rgba(26,28,46,0.6)] p-4 backdrop-blur-[12px] before:pointer-events-none before:absolute before:inset-0 before:rounded-sm ${colorClass}`}
    >
      {player.isDead && (
        <div className="absolute top-2 right-2">
          <Icon name="skull" className="relative w-4 h-4 text-neutral-400" />
        </div>
      )}
      {player.role && roleIcon[player.role] && (
        <Icon name={roleIcon[player.role]} className="relative w-8 h-8 text-neutral-400" />
      )}
      <Typography variant="body" bold textColor="text" className="relative text-center">
        {player.username}
      </Typography>
      {player.role && (
        <Typography variant="body-sm" textColor="neutral-400" className="relative text-center">
          {t(`components.pages.recap.role.${player.role}`)}
        </Typography>
      )}
    </div>
  );
};

export default PlayerCard;
