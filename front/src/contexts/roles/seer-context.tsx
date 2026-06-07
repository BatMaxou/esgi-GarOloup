'use client';

import { createContext, ReactNode, useCallback, useContext } from 'react';

import { GameRoleEnum } from '@/utils/enums';
import { BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useApiClient } from '@/contexts/api-context';
import { usePlayer } from '@/contexts/player-context';

type Props = {
  children: ReactNode;
};

type SeerContextType = {
  lastObservedPlayerId: string | null;
  lastObservedRole: GameRoleEnum | null;
  observedRoles: Record<string, GameRoleEnum>;
  reveal: (targetPlayerId: string) => Promise<BasicActionResponse | ApiClientError>;
};

export const SeerContext = createContext<SeerContextType | undefined>(undefined);

export const SeerProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const { player } = usePlayer();

  const lastObservedPlayerId = player?.role?.lastObservedPlayerId ?? null;
  const lastObservedRole = player?.role?.lastObservedRole ?? null;
  const observedRoles = player?.role?.observedRoles ?? {};

  const reveal = useCallback((targetPlayerId: string) => apiClient.seer.reveal(targetPlayerId), [apiClient]);

  return (
    <SeerContext.Provider value={{ lastObservedPlayerId, lastObservedRole, observedRoles, reveal }}>
      {children}
    </SeerContext.Provider>
  );
};

export const useSeer = () => {
  const context = useContext(SeerContext);
  if (!context) {
    throw new Error('useSeer must be used within a SeerProvider');
  }

  return context;
};
