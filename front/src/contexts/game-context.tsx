'use client';

import { createContext, ReactNode, useCallback, useContext, useEffect, useRef, useState } from 'react';

import type { Game } from '@/utils/types';
import { useApiClient } from '@/contexts/api-context';
import { useAuth } from '@/contexts/auth-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useMercureClient } from '@/contexts/mercure-context';
import { usePlayer } from '@/contexts/player-context';

type Props = {
  children: ReactNode;
  initialGame?: Game | null;
};

type GameContextType = {
  game: Game | null;
  setGame: (game: Game | null) => void;
  leaveGame: () => void;
};

export const GameContext = createContext<GameContextType | undefined>(undefined);

export const GameProvider = ({ children, initialGame = null }: Props) => {
  const [game, setGame] = useState<Game | null>(initialGame);
  const isWatching = useRef<boolean>(false);
  const { apiClient } = useApiClient();
  const { mercureClient, isCredentialsInitialized } = useMercureClient();
  const { user } = useAuth();
  const { desyncPlayer } = usePlayer();

  const leaveGame = useCallback(() => {
    isWatching.current = false;
    setGame(null);
    desyncPlayer();
  }, [desyncPlayer]);

  useEffect(() => {
    if (game) {
      return;
    }

    if (user) {
      apiClient.game.getCurrent().then((maybeGame) => {
        if (!(maybeGame instanceof ApiClientError)) {
          setGame(maybeGame);
        }
      });
    }
  }, [apiClient, user, game]);

  useEffect(() => {
    if (!game?.id || !mercureClient || isWatching.current || !isCredentialsInitialized) {
      return;
    }

    isWatching.current = true;
    const eventSource = mercureClient.watchGame(game.id, setGame);

    return () => {
      isWatching.current = false;
      eventSource?.close();
    };
  }, [mercureClient, game?.id, isCredentialsInitialized]);

  return <GameContext.Provider value={{ game, setGame, leaveGame }}>{children}</GameContext.Provider>;
};

export const useGame = () => {
  const context = useContext(GameContext);
  if (!context) {
    throw new Error('useGame must be used within a GameProvider');
  }

  return context;
};
