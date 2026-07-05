'use client';

import { createContext, ReactNode, useCallback, useContext } from 'react';

import { GameRoleEnum } from '@/utils/enums';
import { CupidonRole, GameRole } from '@/utils/types';
import { BasicActionResponse } from '@/lib/api/ApiClient';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useApiClient } from '@/contexts/api-context';
import { usePlayer } from '@/contexts/player-context';

type Props = {
  children: ReactNode;
};

type CupidonContextType = {
  hasFormedCouple: boolean;
  setup: (firstLoverId: string, secondLoverId: string) => Promise<BasicActionResponse | ApiClientError>;
};

const isCupidonRole = (role?: GameRole): role is CupidonRole => role?.type === GameRoleEnum.CUPIDON;

export const CupidonContext = createContext<CupidonContextType | undefined>(undefined);

export const CupidonProvider = ({ children }: Props) => {
  const { apiClient } = useApiClient();
  const { player } = usePlayer();

  const playerRole = player?.role;
  const role = isCupidonRole(playerRole) ? playerRole : undefined;

  const hasFormedCouple = role?.setup ?? false;

  const setup = useCallback(
    (firstLoverId: string, secondLoverId: string) => apiClient.cupidon.setup(firstLoverId, secondLoverId),
    [apiClient]
  );

  return <CupidonContext.Provider value={{ hasFormedCouple, setup }}>{children}</CupidonContext.Provider>;
};

export const useCupidon = () => {
  const context = useContext(CupidonContext);
  if (!context) {
    throw new Error('useCupidon must be used within a CupidonProvider');
  }

  return context;
};
