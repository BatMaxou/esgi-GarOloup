'use client';

import { createContext, ReactNode, useCallback, useContext, useEffect } from 'react';

import { ApiClient } from '@/lib/api/ApiClient';
import { apiBaseUrl } from '@/utils/tools';
import { updateUser, useSession } from '@/lib/auth/auth-client';

type Props = {
  children: ReactNode;
};

type ApiClientContextType = {
  apiClient: ApiClient;
};

export const ApiClientContext = createContext<ApiClientContextType | undefined>(undefined);

const apiClient = new ApiClient(apiBaseUrl);

export const ApiClientProvider = ({ children }: Props) => {
  const { data, refetch } = useSession();

  const propagateChangeToken = useCallback(
    async (token?: string | null, refreshToken?: string | null) => {
      await updateUser({ token, refreshToken });
      await refetch();
    },
    [refetch]
  );

  useEffect(() => {
    apiClient.initPropagateChangeToken(propagateChangeToken);
  }, [propagateChangeToken]);

  useEffect(() => {
    apiClient.token = data?.user?.token ?? null;
    apiClient.refreshToken = data?.user?.refreshToken ?? null;
  }, [data]);

  return <ApiClientContext.Provider value={{ apiClient }}>{children}</ApiClientContext.Provider>;
};

export const useApiClient = () => {
  const context = useContext(ApiClientContext);
  if (!context) {
    throw new Error('useApiClient must be used within an ApiClientProvider');
  }
  return context;
};
