'use client';

import { createContext, ReactNode, useContext, useEffect, useState } from 'react';

import { mercureUrl } from '@/utils/tools';
import { MercureClient } from '@/lib/mercure/MercureClient';
import { useApiClient } from '@/contexts/api-context';
import { ClientCookieRegistry } from '@/lib/cookie/ClientCookieRegistry';

type Props = {
  children: ReactNode;
};

type MercureClientContextType = {
  mercureClient: MercureClient;
  isCredentialsInitialized: boolean;
};

export const MercureClientContext = createContext<MercureClientContextType | undefined>(undefined);

export const MercureClientProvider = ({ children }: Props) => {
  const [isCredentialsInitialized, setIsCredentialsInitialized] = useState(false);
  const { apiClient, tokenHasChanged } = useApiClient();
  const [mercureClient] = useState(new MercureClient(mercureUrl, apiClient, new ClientCookieRegistry()));

  useEffect(() => {
    if (isCredentialsInitialized) {
      return;
    }

    if (tokenHasChanged) {
      mercureClient.fetchCredentials().then(() => setIsCredentialsInitialized(true));
    }
  }, [mercureClient, tokenHasChanged, isCredentialsInitialized]);

  return (
    <MercureClientContext.Provider value={{ mercureClient, isCredentialsInitialized }}>
      {children}
    </MercureClientContext.Provider>
  );
};

export const useMercureClient = () => {
  const context = useContext(MercureClientContext);
  if (!context) {
    throw new Error('useMercureClient must be used within an MercureClientProvider');
  }

  return context;
};
