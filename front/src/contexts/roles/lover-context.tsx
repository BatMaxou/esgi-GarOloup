'use client';

import { createContext, ReactNode, useContext, useMemo } from 'react';

import { GameRole, LoverRole, Player } from '@/utils/types';
import { useGame } from '@/contexts/game-context';
import { usePlayer } from '@/contexts/player-context';

type Props = {
  children: ReactNode;
};

type LoverContextType = {
  isLover: boolean;
  partnerPlayerId: string | null;
  partner: Player | null;
};

export const isLoverRole = (role?: GameRole): role is LoverRole =>
  Boolean(role && 'partnerPlayerId' in role && role.partnerPlayerId);

export const LoverContext = createContext<LoverContextType | undefined>(undefined);

export const LoverProvider = ({ children }: Props) => {
  const { game } = useGame();
  const { player } = usePlayer();

  const playerRole = player?.role;
  const role = isLoverRole(playerRole) ? playerRole : undefined;

  const partnerPlayerId = role?.partnerPlayerId ?? null;

  const partner = useMemo(() => {
    if (!partnerPlayerId) {
      return null;
    }

    return game?.players?.find((currentPlayer) => currentPlayer.id === partnerPlayerId) ?? null;
  }, [game?.players, partnerPlayerId]);

  return (
    <LoverContext.Provider value={{ isLover: Boolean(role), partnerPlayerId, partner }}>
      {children}
    </LoverContext.Provider>
  );
};

export const useLover = () => {
  const context = useContext(LoverContext);
  if (!context) {
    throw new Error('useLover must be used within a LoverProvider');
  }

  return context;
};
