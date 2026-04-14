'use client';

import { createContext, ReactNode, useContext, useEffect, useRef, useState } from 'react';

import type { WerewolfTeam } from '@/utils/types';
import { useApiClient } from '@/contexts/api-context';
import { useAuth } from '@/contexts/auth-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useMercureClient } from '@/contexts/mercure-context';

type Props = {
  children: ReactNode;
};

type WerewolfContextType = {
  team: WerewolfTeam | null;
};

export const WereWolfContext = createContext<WerewolfContextType | undefined>(undefined);

export const WereWolfProvider = ({ children }: Props) => {
  const [team, setTeam] = useState<WerewolfTeam | null>(null);
  const isWatching = useRef<boolean>(false);
  const { apiClient } = useApiClient();
  const { mercureClient, isCredentialsInitialized } = useMercureClient();
  const { user } = useAuth();

  useEffect(() => {
    if (team) {
      return;
    }

    if (user) {
      apiClient.werewolf.getTeam().then((maybeTeam) => {
        if (!(maybeTeam instanceof ApiClientError)) {
          setTeam(maybeTeam);
        }
      });
    }
  }, [apiClient, user, team]);

  useEffect(() => {
    if (!team?.gameId || !mercureClient || isWatching.current || !isCredentialsInitialized) {
      return;
    }

    isWatching.current = true;
    const eventSource = mercureClient.watchWerewolfTeam(team.gameId, setTeam);

    return () => {
      isWatching.current = false;
      eventSource?.close();
    };
  }, [mercureClient, team?.gameId, isCredentialsInitialized]);

  return <WereWolfContext.Provider value={{ team }}>{children}</WereWolfContext.Provider>;
};

export const useWerewolf = () => {
  const context = useContext(WereWolfContext);
  if (!context) {
    throw new Error('useWerewolf must be used within a WereWolfProvider');
  }

  return context;
};
