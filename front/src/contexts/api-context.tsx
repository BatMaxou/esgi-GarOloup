'use client';

import { createContext, ReactNode, useContext, useEffect } from 'react';

import { ApiClient } from '@/lib/api/ApiClient';
import { apiBaseUrl } from '@/utils/tools';
import { ClientCookieRegistry } from '@/lib/cookie/ClientCookieRegistry';

type Props = {
  children: ReactNode;
};

type ApiClientContextType = {
  apiClient: ApiClient;
};

export const ApiClientContext = createContext<ApiClientContextType | undefined>(undefined);

const apiClient = new ApiClient(apiBaseUrl, new ClientCookieRegistry());

export const ApiClientProvider = ({ children }: Props) => {
  useEffect(() => {
    apiClient.retrieveTokens();
  }, []);

  return <ApiClientContext.Provider value={{ apiClient }}>{children}</ApiClientContext.Provider>;
};

export const useApiClient = () => {
  const context = useContext(ApiClientContext);
  if (!context) {
    throw new Error('useApiClient must be used within an ApiClientProvider');
  }

  return context;
};
