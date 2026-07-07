'use client';

import { createContext, ReactNode, useCallback, useContext, useEffect, useRef, useState } from 'react';

import { toast } from 'react-toastify';
import { useTranslations } from 'next-intl';
import { useApiClient } from '@/contexts/api-context';
import { useAuth } from '@/contexts/auth-context';
import { useMercureClient } from '@/contexts/mercure-context';
import { usePlayer } from '@/contexts/player-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { CollectionResponse } from '@/lib/api/ApiClient';
import { GameConfigurationPayload } from '@/lib/api/resources/GameResource';
import type { Game, RoleDispatchEntry } from '@/utils/types';

type Props = {
  children: ReactNode;
  initialGame?: Game | null;
};

type GameContextType = {
  game: Game | null;
  setGame: (game: Game | null) => void;
  leaveGame: () => void;
  publicGames: CollectionResponse<Game> | null;
  publicGamesLoading: boolean;
  getPublicGames: (page: number, itemsPerPage?: number) => void;
  openInvitation: () => void;
  closeInvitation: () => void;
  setConfiguration: (configuration: GameConfigurationPayload) => void;
  resetConfiguration: () => void;
  setGameMaster: (playerId: string) => void;
  resetGameMaster: () => void;
  dispatchRoles: (payload: RoleDispatchEntry[]) => void;
  resetRoleDispatch: () => void;
  launchGame: () => void;
  vote: (targetPlayerId: string) => Promise<void>;
  timeUp: () => void;
};

export const GameContext = createContext<GameContextType | undefined>(undefined);

export const GameProvider = ({ children, initialGame = null }: Props) => {
  const [game, setGame] = useState<Game | null>(initialGame);
  const [publicGamesLoading, setPublicGamesLoading] = useState<boolean>(false);
  const [publicGames, setPublicGames] = useState<CollectionResponse<Game> | null>(null);
  const isWatching = useRef<boolean>(false);
  const { apiClient } = useApiClient();
  const t = useTranslations('contexts.game');
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

  const getPublicGames = async (page: number = 1, itemsPerPage?: number) => {
    setPublicGamesLoading(true);
    const response = await apiClient.game.getPublics(page, itemsPerPage);
    if (response instanceof ApiClientError) {
      setPublicGamesLoading(false);
      toast.error(t('getPublicGamesError'));
      return;
    }
    setPublicGames(response as CollectionResponse<Game>);
    setPublicGamesLoading(false);
  };

  const openInvitation = async () => {
    const response = await apiClient.game.open();
    if (response instanceof ApiClientError) {
      toast.error(t('openInvitationError'));
      return;
    }
    toast.success(t('openInvitationSuccess'));
  };

  const closeInvitation = async () => {
    const response = await apiClient.game.close();
    if (response instanceof ApiClientError) {
      toast.error(t('closeInvitationError'));
      return;
    }
    toast.success(t('closeInvitationSuccess'));
  };

  const setConfiguration = async (configuration: GameConfigurationPayload) => {
    const response = await apiClient.game.setConfiguration(configuration);
    if (response instanceof ApiClientError) {
      toast.error(t('setConfigurationError'));
      return;
    }
    toast.success(t('setConfigurationSuccess'));
  };

  const resetConfiguration = async () => {
    const response = await apiClient.game.resetConfiguration();
    if (response instanceof ApiClientError) {
      toast.error(t('resetConfigurationError'));
      return;
    }
  };

  const setGameMaster = async (playerId: string) => {
    const response = await apiClient.game.setGameMaster(playerId);
    if (response instanceof ApiClientError) {
      toast.error(t('setGameMasterError'));
      return;
    }
    toast.success(t('setGameMasterSuccess'));
  };

  const resetGameMaster = async () => {
    const response = await apiClient.game.resetGameMaster();
    if (response instanceof ApiClientError) {
      toast.error(t('resetGameMasterError'));
      return;
    }
  };

  const dispatchRoles = async (payload: RoleDispatchEntry[]) => {
    const response = await apiClient.game.dispatchRoles(payload);
    if (response instanceof ApiClientError) {
      toast.error(t('dispatchRolesError'));
      return;
    }
  };

  const resetRoleDispatch = async () => {
    const response = await apiClient.game.resetRoleDispatch();
    if (response instanceof ApiClientError) {
      toast.error(t('resetRoleDispatchError'));
      return;
    }
  };

  const launchGame = async () => {
    const response = await apiClient.game.launch();
    if (response instanceof ApiClientError) {
      toast.error(t('launchGameError'));
      return;
    }
    toast.success(t('launchGameSuccess'));
  };

  const vote = async (targetPlayerId: string) => {
    const response = await apiClient.game.vote(targetPlayerId);
    if (response instanceof ApiClientError) {
      toast.error(t('voteError'));
      return;
    }
  };

  const timeUp = async () => {
    await apiClient.game.timeUp();
  };

  return (
    <GameContext.Provider
      value={{
        game,
        setGame,
        leaveGame,
        publicGames,
        publicGamesLoading,
        getPublicGames,
        openInvitation,
        closeInvitation,
        setConfiguration,
        resetConfiguration,
        setGameMaster,
        resetGameMaster,
        dispatchRoles,
        resetRoleDispatch,
        launchGame,
        vote,
        timeUp,
      }}
    >
      {children}
    </GameContext.Provider>
  );
};

export const useGame = () => {
  const context = useContext(GameContext);
  if (!context) {
    throw new Error('useGame must be used within a GameProvider');
  }

  return context;
};
