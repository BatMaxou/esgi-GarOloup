'use client';

import { createContext, ReactNode, useCallback, useContext } from 'react';

import { GameRoleEnum } from '@/utils/enums';
import { GameRole, HunterRole } from '@/utils/types';
import { BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useApiClient } from '@/contexts/api-context';
import { usePlayer } from '@/contexts/player-context';

type Props = {
  children: ReactNode;
};

type HunterContextType = {
  hasShot: boolean;
  shoot: (targetPlayerId: string) => Promise<BasicActionResponse | ApiClientError>;
};

const isHunterRole = (role?: GameRole): role is HunterRole => role?.type === GameRoleEnum.HUNTER;

export const HunterContext = createContext<HunterContextType | undefined>(undefined);

export const HunterProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const { player } = usePlayer();

  const playerRole = player?.role;
  const role = isHunterRole(playerRole) ? playerRole : undefined;

  const hasShot = role?.hasShot ?? false;

  const shoot = useCallback((targetPlayerId: string) => apiClient.hunter.shoot(targetPlayerId), [apiClient]);

  return <HunterContext.Provider value={{ hasShot, shoot }}>{children}</HunterContext.Provider>;
};

export const useHunter = () => {
  const context = useContext(HunterContext);
  if (!context) {
    throw new Error('useHunter must be used within a HunterProvider');
  }

  return context;
};
