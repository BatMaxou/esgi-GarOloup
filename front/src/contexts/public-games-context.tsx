'use client';

import { createContext, ReactNode, useContext, useState } from 'react';

import type { Game } from '@/utils/types';
import { useApiClient } from '@/contexts/api-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { CollectionResponse } from '@/lib/api/ApiClient';
import { toast } from 'react-toastify';
import { useTranslations } from 'next-intl';

type Props = {
  children: ReactNode;
};

type PublicGamesContextType = {
  publicGames: CollectionResponse<Game> | null;
  publicGamesLoading: boolean;
  getPublicGames: (page: number, itemsPerPage?: number) => void;
};

export const PublicGamesContext = createContext<PublicGamesContextType | undefined>(undefined);

export const PublicGamesProvider = ({ children }: Props) => {
  const [publicGamesLoading, setPublicGamesLoading] = useState<boolean>(false);
  const [publicGames, setPublicGames] = useState<CollectionResponse<Game> | null>(null);
  const { apiClient } = useApiClient();
  const t = useTranslations('contexts.game');

  const getPublicGames = async (page: number = 1, itemsPerPage?: number) => {
    setPublicGamesLoading(true);
    const response = await apiClient.game.getPublics(page, itemsPerPage);
    if (response instanceof ApiClientError) {
      setPublicGamesLoading(false);
      toast.error(t('getPublicGamesError'));
      return;
    }
    setPublicGames(response);
    setPublicGamesLoading(false);
  };

  return (
    <PublicGamesContext.Provider value={{ publicGames, publicGamesLoading, getPublicGames }}>
      {children}
    </PublicGamesContext.Provider>
  );
};

export const usePublicGames = () => {
  const context = useContext(PublicGamesContext);
  if (!context) {
    throw new Error('usePublicGames must be used within a PublicGamesProvider');
  }

  return context;
};
