'use client';

import { createContext, ReactNode, useCallback, useContext } from 'react';

import { GameRoleEnum } from '@/utils/enums';
import { AssassinRole, GameRole } from '@/utils/types';
import { BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useApiClient } from '@/contexts/api-context';
import { usePlayer } from '@/contexts/player-context';

type Props = {
  children: ReactNode;
};

type AssassinContextType = {
  actedThisNight: boolean;
  kill: (targetPlayerId: string) => Promise<BasicActionResponse | ApiClientError>;
};

const isAssassinRole = (role?: GameRole): role is AssassinRole => role?.type === GameRoleEnum.ASSASSIN;

export const AssassinContext = createContext<AssassinContextType | undefined>(undefined);

export const AssassinProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const { player } = usePlayer();

  const playerRole = player?.role;
  const role = isAssassinRole(playerRole) ? playerRole : undefined;

  const actedThisNight = role?.actedThisNight ?? false;

  const kill = useCallback((targetPlayerId: string) => apiClient.assassin.kill(targetPlayerId), [apiClient]);

  return <AssassinContext.Provider value={{ actedThisNight, kill }}>{children}</AssassinContext.Provider>;
};

export const useAssassin = () => {
  const context = useContext(AssassinContext);
  if (!context) {
    throw new Error('useAssassin must be used within an AssassinProvider');
  }

  return context;
};
