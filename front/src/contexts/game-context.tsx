'use client';

import {
  createContext,
  ReactNode,
  useContext,
  useEffect,
  useState,
} from 'react';

import type { Game } from '@/utils/types';
import { useApiClient } from './api-context';
import { useAuth } from './auth-context';
import { ApiClientError } from '@/lib/api/ApiClientError';

type Props = {
  children: ReactNode;
};

type GameContextType = {
  game: Game | null;
  setGame: (game: Game | null) => void;
};

export const GameContext = createContext<GameContextType | undefined>(
  undefined
);

export const GameProvider = ({ children }: Props) => {
  const [game, setGame] = useState<Game | null>(null);
  const { apiClient } = useApiClient();
  const { user } = useAuth();

  useEffect(() => {
    if (user) {
      apiClient.game.getCurrent().then((maybeGame) => {
        if (!(maybeGame instanceof ApiClientError)) {
          setGame(maybeGame);
        }
      });
    }
  }, [apiClient, user]);

  return (
    <GameContext.Provider
      value={{
        game,
        setGame,
      }}
    >
      {children}
    </GameContext.Provider>
  );
};

export const useGame = () => {
  const context = useContext(GameContext);
  if (!context) {
    throw new Error('useGame must be used within an gameProvider');
  }

  return context;
};
