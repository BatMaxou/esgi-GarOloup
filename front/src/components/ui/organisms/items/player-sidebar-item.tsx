'use client';

import { usePlayer } from '@/contexts/player-context';
import type { Player } from '@/utils/types';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Icon from '../../atoms/icon';

type Props = {
  player: Player;
};

const PlayerSidebarItem = ({ player }: Props) => {
  const { player: currentPlayer } = usePlayer();

  const linkedUser = player.user ?? player.tempUser;
  if (!linkedUser) {
    return null;
  }

  const userName = linkedUser.username ?? '';
  const isCurrentPlayer = currentPlayer?.id === player.id;
  const isDead = player.dead;

  return (
    <Card variant="player" isCurrentPlayer={isCurrentPlayer} liftOnHover={false} hoverable fullfilled>
      <div className="flex flex-row items-center justify-between px-4 py-3">
        <Typography tag="p" variant="body-sm" textColor="primary" bold>
          {isCurrentPlayer ? `Toi (${userName})` : userName}
        </Typography>

        {isDead && (
          <Typography tag="p" variant="body-sm" textColor="primary" bold>
            <Icon name="skull" className="w-4 h-4 color-white" />
          </Typography>
        )}
      </div>
    </Card>
  );
};

export default PlayerSidebarItem;
