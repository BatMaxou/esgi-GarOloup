'use client';

import { createContext, ReactNode, useCallback, useContext } from 'react';

import { GameRoleEnum } from '@/utils/enums';
import { GameRole, WitchRole } from '@/utils/types';
import { BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useApiClient } from '@/contexts/api-context';
import { usePlayer } from '@/contexts/player-context';

type Props = {
  children: ReactNode;
};

type WitchContextType = {
  healPotionAvailable: boolean;
  poisonPotionAvailable: boolean;
  save: (targetPlayerId: string) => Promise<BasicActionResponse | ApiClientError>;
  poison: (targetPlayerId: string) => Promise<BasicActionResponse | ApiClientError>;
  pass: () => Promise<BasicActionResponse | ApiClientError>;
};

const isWitchRole = (role?: GameRole): role is WitchRole => role?.type === GameRoleEnum.WITCH;

export const WitchContext = createContext<WitchContextType | undefined>(undefined);

export const WitchProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const { player } = usePlayer();

  const playerRole = player?.role;
  const role = isWitchRole(playerRole) ? playerRole : undefined;

  const healPotionAvailable = role?.healPotionAvailable ?? false;
  const poisonPotionAvailable = role?.poisonPotionAvailable ?? false;

  const save = useCallback((targetPlayerId: string) => apiClient.witch.save(targetPlayerId), [apiClient]);
  const poison = useCallback((targetPlayerId: string) => apiClient.witch.poison(targetPlayerId), [apiClient]);
  const pass = useCallback(() => apiClient.game.passTurn(), [apiClient]);

  return (
    <WitchContext.Provider value={{ healPotionAvailable, poisonPotionAvailable, save, poison, pass }}>
      {children}
    </WitchContext.Provider>
  );
};

export const useWitch = () => {
  const context = useContext(WitchContext);
  if (!context) {
    throw new Error('useWitch must be used within a WitchProvider');
  }

  return context;
};
