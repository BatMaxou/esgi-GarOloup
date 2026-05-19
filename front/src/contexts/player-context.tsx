'use client';

import { createContext, ReactNode, useCallback, useContext, useEffect, useRef, useState } from 'react';

import type { Player } from '@/utils/types';
import { useApiClient } from '@/contexts/api-context';
import { useAuth } from '@/contexts/auth-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useMercureClient } from '@/contexts/mercure-context';

type Props = {
  children: ReactNode;
  initialPlayer?: Player | null;
};

type PlayerContextType = {
  player: Player | null;
  setPlayer: (player: Player | null) => void;
  desyncPlayer: () => void;
};

export const PlayerContext = createContext<PlayerContextType | undefined>(undefined);

export const PlayerProvider = ({ children, initialPlayer = null }: Props) => {
  const [player, setPlayer] = useState<Player | null>(initialPlayer);
  const isWatching = useRef<boolean>(false);
  const { apiClient } = useApiClient();
  const { mercureClient, isCredentialsInitialized, requestMercureTokenRefresh } = useMercureClient();
  const { user } = useAuth();

  const desyncPlayer = useCallback(() => {
    isWatching.current = false;
    setPlayer(null);
  }, []);

  useEffect(() => {
    if (player) {
      return;
    }

    if (user) {
      apiClient.player.getCurrent().then((maybePlayer) => {
        if (!(maybePlayer instanceof ApiClientError)) {
          setPlayer(maybePlayer);
        }
      });
    }
  }, [apiClient, user, player]);

  useEffect(() => {
    if (!player?.id) {
      return;
    }

    requestMercureTokenRefresh();
  }, [player?.id, requestMercureTokenRefresh]);

  useEffect(() => {
    if (!player?.id || !mercureClient || isWatching.current || !isCredentialsInitialized) {
      return;
    }

    isWatching.current = true;
    const eventSource = mercureClient.watchPlayer(player.id, setPlayer);

    return () => {
      isWatching.current = false;
      eventSource?.close();
    };
  }, [mercureClient, player?.id, isCredentialsInitialized]);

  return <PlayerContext.Provider value={{ player, setPlayer, desyncPlayer }}>{children}</PlayerContext.Provider>;
};

export const usePlayer = () => {
  const context = useContext(PlayerContext);
  if (!context) {
    throw new Error('usePlayer must be used within a PlayerProvider');
  }

  return context;
};
