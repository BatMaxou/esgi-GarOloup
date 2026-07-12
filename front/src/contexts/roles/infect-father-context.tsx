'use client';

import { createContext, ReactNode, useCallback, useContext } from 'react';

import { GameRoleEnum } from '@/utils/enums';
import { GameRole, InfectFatherRole } from '@/utils/types';
import { BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useApiClient } from '@/contexts/api-context';
import { usePlayer } from '@/contexts/player-context';

type Props = {
  children: ReactNode;
};

type InfectFatherContextType = {
  infectionAvailable: boolean;
  infect: () => Promise<BasicActionResponse | ApiClientError>;
  pass: () => Promise<BasicActionResponse | ApiClientError>;
};

const isInfectFatherRole = (role?: GameRole): role is InfectFatherRole => role?.type === GameRoleEnum.INFECT_FATHER;

export const InfectFatherContext = createContext<InfectFatherContextType | undefined>(undefined);

export const InfectFatherProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const { player } = usePlayer();

  const playerRole = player?.role;
  const role = isInfectFatherRole(playerRole) ? playerRole : undefined;

  const infectionAvailable = role?.infectionAvailable ?? false;

  const infect = useCallback(() => apiClient.infectFather.infect(), [apiClient]);
  const pass = useCallback(() => apiClient.game.passTurn(), [apiClient]);

  return (
    <InfectFatherContext.Provider value={{ infectionAvailable, infect, pass }}>{children}</InfectFatherContext.Provider>
  );
};

export const useInfectFather = () => {
  const context = useContext(InfectFatherContext);
  if (!context) {
    throw new Error('useInfectFather must be used within an InfectFatherProvider');
  }

  return context;
};
