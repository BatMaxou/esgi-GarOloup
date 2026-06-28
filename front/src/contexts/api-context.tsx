'use client';

import { createContext, ReactNode, useCallback, useContext, useEffect, useState } from 'react';

import { ApiClient } from '@/lib/api/ApiClient';
import type { User } from '@/utils/types';
import { apiBaseUrl } from '@/utils/tools';
import { updateUser, useSession } from '@/lib/auth/auth-client';
import { tempUserUpdateUser, useTempUserSession } from '@/lib/auth/auth-temp-user-client';

type Props = {
  children: ReactNode;
};

type ApiClientContextType = {
  apiClient: ApiClient;
  tokenHasChanged: boolean;
  isTokenInitialized: boolean;
};

export const ApiClientContext = createContext<ApiClientContextType | undefined>(undefined);

const apiClient = new ApiClient(apiBaseUrl);

export const ApiClientProvider = ({ children }: Props) => {
  const { data: mainSession, refetch: refetchMain } = useSession();
  const { data: tempSession, refetch: refetchTemp } = useTempUserSession();
  const [tokenHasChanged, setTokenHasChanged] = useState(false);
  const [isTokenInitialized, setIsTokenInitialized] = useState(!!apiClient.token);

  const propagateChangeToken = useCallback(
    async (token?: string | null, refreshToken?: string | null) => {
      if (mainSession?.user) {
        await updateUser({ token, refreshToken });
        await refetchMain();
        return;
      }
      if (tempSession?.user) {
        await tempUserUpdateUser({ token, refreshToken } as Parameters<typeof tempUserUpdateUser>[0]);
        await refetchTemp();
      }
    },
    [mainSession?.user, refetchMain, refetchTemp, tempSession?.user]
  );

  useEffect(() => {
    apiClient.initPropagateChangeToken(propagateChangeToken);
  }, [propagateChangeToken]);

  useEffect(() => {
    if (tokenHasChanged) {
      setTokenHasChanged(false); // eslint-disable-line react-hooks/set-state-in-effect
    }
  }, [tokenHasChanged]);

  useEffect(() => {
    const user = (mainSession?.user ?? tempSession?.user) as User | null | undefined;
    const token = user?.token ?? null;
    const tokenChanged = apiClient.token !== token;

    apiClient.token = token;
    apiClient.refreshToken = user?.refreshToken ?? null;

    if (token && tokenChanged) {
      setTokenHasChanged(true); // eslint-disable-line react-hooks/set-state-in-effect
    }

    setIsTokenInitialized(!!token);
  }, [mainSession?.user, tempSession?.user]);

  return (
    <ApiClientContext.Provider value={{ apiClient, tokenHasChanged, isTokenInitialized }}>
      {children}
    </ApiClientContext.Provider>
  );
};

export const useApiClient = () => {
  const context = useContext(ApiClientContext);
  if (!context) {
    throw new Error('useApiClient must be used within an ApiClientProvider');
  }
  return context;
};
