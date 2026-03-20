'use client';

import { createContext, ReactNode, useContext, useEffect } from 'react';

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

const apiClient = new ApiClient(apiBaseUrl, null, null, (token, refreshToken) => {
  updateUser({ token, refreshToken });
});

export const ApiClientProvider = ({ children }: Props) => {
  const { data } = useSession();

  useEffect(() => {
    apiClient.changeToken(data?.user?.token, data?.user?.refreshToken);
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
