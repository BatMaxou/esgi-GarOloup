'use client';

import { createContext, ReactNode, useContext, useEffect, useState } from 'react';

import type { Player } from '@/utils/types';
import { useApiClient } from './api-context';
import { useAuth } from './auth-context';
import { ApiClientError } from '@/lib/api/ApiClientError';

type Props = {
  children: ReactNode;
};

type PlayerContextType = {
  player: Player | null;
  setPlayer: (player: Player | null) => void;
};

export const PlayerContext = createContext<PlayerContextType | undefined>(undefined);

export const PlayerProvider = ({ children }: Props) => {
  const [player, setPlayer] = useState<Player | null>(null);
  const { apiClient } = useApiClient();
  const { user } = useAuth();

  useEffect(() => {
    if (user) {
      apiClient.player.getCurrent().then((maybePlayer) => {
        if (!(maybePlayer instanceof ApiClientError)) {
          setPlayer(maybePlayer);
        }
      });
    }
  }, [apiClient, user]);

  return (
    <PlayerContext.Provider
      value={{
        player,
        setPlayer,
      }}
    >
      {children}
    </PlayerContext.Provider>
  );
};

export const usePlayer = () => {
  const context = useContext(PlayerContext);
  if (!context) {
    throw new Error('usePlayer must be used within an playerProvider');
  }

  return context;
};
