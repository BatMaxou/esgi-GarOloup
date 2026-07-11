'use client';

import { useContext } from 'react';

import { usePlayer } from '@/contexts/player-context';
import type { Player } from '@/utils/types';
import Typography from '@/components/ui/atoms/typography';
import Card from '@/components/ui/molecules/card';
import Icon from '../../atoms/icon';
import { useGame } from '@/contexts/game-context';
import { WereWolfContext } from '@/contexts/roles/werewolf-context';
import { useTranslations } from 'next-intl';
import PlayerLoverPartnerIcon from '../../molecules/icon/player-lover-partner-item';
import { isLoverRole } from '@/contexts/roles/lover-context';

type Props = {
  player: Player;
};

const PlayerSidebarItem = ({ player }: Props) => {
  const { player: currentPlayer } = usePlayer();
  const { game } = useGame();
  const t = useTranslations('components.ui.organisms.items.playerSidebarItem');
  const werewolfContext = useContext(WereWolfContext);
  const isGameMaster = game?.gameMaster?.id === player.id;
  const isWerewolfTeammate = werewolfContext?.team?.members.some((member) => member.id === player.id) ?? false;

  const linkedUser = player.user ?? player.tempUser;
  if (!linkedUser || !currentPlayer) {
    return null;
  }

  const userName = linkedUser.username ?? '';
  const isCurrentPlayer = currentPlayer?.id === player.id;
  const isDead = player.dead;

  return (
    <Card variant="player" isCurrentPlayer={isCurrentPlayer} liftOnHover={false} hoverable fullfilled>
      <div className="flex flex-row items-center justify-between px-4 py-3 gap-2">
        <Typography tag="p" variant="body-sm" textColor="primary" bold>
          {isCurrentPlayer ? `${t('you')} (${userName})` : userName}
        </Typography>

        {isWerewolfTeammate && (
          <Typography tag="p" variant="body-sm" textColor="error" bold>
            <Icon name="werewolf" className="w-4 h-4 color-error" />
          </Typography>
        )}

        {isGameMaster && (
          <Typography tag="p" variant="body-sm" textColor="accent" bold>
            <Icon name="crown" className="w-4 h-4 color-accent" />
          </Typography>
        )}

        {isDead && (
          <Typography tag="p" variant="body-sm" textColor="primary" bold>
            <Icon name="skull" className="w-4 h-4 color-white" />
          </Typography>
        )}

        {isLoverRole(currentPlayer?.role) && <PlayerLoverPartnerIcon currentPlayer={currentPlayer} player={player} />}
      </div>
    </Card>
  );
};

export default PlayerSidebarItem;
