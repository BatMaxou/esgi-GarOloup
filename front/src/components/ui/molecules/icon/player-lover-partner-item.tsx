'use client';

import { Player } from '@/utils/types';
import Icon from '../../atoms/icon';
import { isLoverRole, useLover } from '@/contexts/roles/lover-context';

const PlayerLoverPartnerIcon = ({ currentPlayer, player }: { currentPlayer: Player; player: Player }) => {
  const { partnerPlayerId } = useLover();
  if (partnerPlayerId === player.id || isLoverRole(currentPlayer.role)) {
    return <Icon name="heart" className="w-5 h-5 text-pink-700" />;
  }
  if (partnerPlayerId === currentPlayer.id) {
    return <Icon name="heart" className="w-5 h-5 text-pink-700" />;
  }
  return;
};

export default PlayerLoverPartnerIcon;
