'use client';

import { createContext, ReactNode, useContext } from 'react';

import { ApiClient } from '@/lib/api/ApiClient';
import { apiBaseUrl } from '@/utils/tools';
import { ClientCookieRegistry } from '@/lib/cookie/ClientCookieRegistry';

type Props = {
  children: ReactNode;
};

type ApiClientContextType = {
  apiClient: ApiClient;
};

export const ApiClientContext = createContext<ApiClientContextType | undefined>(
  undefined
);

export const ApiClientProvider = ({ children }: Props) => {
  return (
    <ApiClientContext.Provider
      value={{
        apiClient: new ApiClient(apiBaseUrl, new ClientCookieRegistry()),
      }}
    >
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
