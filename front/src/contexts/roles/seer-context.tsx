'use client';

import { createContext, ReactNode, useCallback, useContext } from 'react';

import { GameRoleEnum } from '@/utils/enums';
import { GameRole, SeerRole } from '@/utils/types';
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

const isSeerRole = (role?: GameRole): role is SeerRole => role?.type === GameRoleEnum.SEER;

export const SeerContext = createContext<SeerContextType | undefined>(undefined);

export const SeerProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const { player, setPlayer } = usePlayer();

  const playerRole = player?.role;
  const role = isSeerRole(playerRole) ? playerRole : undefined;

  const lastObservedPlayerId = role?.lastObservedPlayerId ?? null;
  const lastObservedRole = role?.lastObservedRole ?? null;
  const observedRoles = role?.observedRoles ?? {};

  const reveal = useCallback(
    async (targetPlayerId: string) => {
      const response = await apiClient.seer.reveal(targetPlayerId);

      if (!(response instanceof ApiClientError)) {
        const updatedPlayer = await apiClient.player.getCurrent();
        if (!(updatedPlayer instanceof ApiClientError)) {
          setPlayer(updatedPlayer);
        }
      }

      return response;
    },
    [apiClient, setPlayer]
  );

  return (
    <SeerContext.Provider value={{ lastObservedPlayerId, lastObservedRole, observedRoles, reveal }}>
      {children}
    </SeerContext.Provider>
  );
};

export const useOptionalSeer = () => useContext(SeerContext) ?? null;

export const useSeer = () => {
  const context = useContext(SeerContext);
  if (!context) {
    throw new Error('useSeer must be used within a SeerProvider');
  }

  return context;
};
