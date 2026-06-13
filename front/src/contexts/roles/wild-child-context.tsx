'use client';

import { createContext, ReactNode, useCallback, useContext } from 'react';

import { GameRoleEnum } from '@/utils/enums';
import { GameRole, WildChildRole } from '@/utils/types';
import { BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useApiClient } from '@/contexts/api-context';
import { usePlayer } from '@/contexts/player-context';

type Props = {
  children: ReactNode;
};

type WildChildContextType = {
  modelPlayerId: string | null;
  isTransformed: boolean;
  setup: (targetPlayerId: string) => Promise<BasicActionResponse | ApiClientError>;
};

const isWildChildRole = (role?: GameRole): role is WildChildRole => role?.type === GameRoleEnum.WILD_CHILD;

export const WildChildContext = createContext<WildChildContextType | undefined>(undefined);

export const WildChildProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const { player } = usePlayer();

  const playerRole = player?.role;
  const role = isWildChildRole(playerRole) ? playerRole : undefined;

  const modelPlayerId = role?.modelPlayerId ?? null;
  const isTransformed = role?.transformed ?? false;

  const setup = useCallback((targetPlayerId: string) => apiClient.wildChild.setup(targetPlayerId), [apiClient]);

  return (
    <WildChildContext.Provider value={{ modelPlayerId, isTransformed, setup }}>{children}</WildChildContext.Provider>
  );
};

export const useWildChild = () => {
  const context = useContext(WildChildContext);
  if (!context) {
    throw new Error('useWildChild must be used within a WildChildProvider');
  }

  return context;
};
