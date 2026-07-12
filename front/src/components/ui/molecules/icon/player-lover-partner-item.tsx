'use client';

import { Player } from '@/utils/types';
import Icon from '../../atoms/icon';
import { useLover } from '@/contexts/roles/lover-context';

const PlayerLoverPartnerIcon = ({ player, className }: { player: Player; className?: string }) => {
  const { partnerPlayerId } = useLover();
  if (partnerPlayerId === player.id) {
    return <Icon name="heart" className={`w-5 h-5 text-pink-500 ${className}`} />;
  }
  return;
};

export default PlayerLoverPartnerIcon;
