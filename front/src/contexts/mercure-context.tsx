'use client';

import { createContext, ReactNode, useContext } from "react"

import { apiBaseUrl, mercureUrl } from "@/utils/tools";
import { MercureClient } from "@/lib/mercure/MercureClient";

type Props = {
  children: ReactNode;
};

type MercureClientContextType = {
  mercureClient: MercureClient;
};

export const MercureClientContext = createContext<MercureClientContextType | undefined>(undefined);

export const MercureClientProvider = ({ children }: Props) => {
  return <MercureClientContext.Provider value={{ mercureClient: new MercureClient(mercureUrl, apiBaseUrl) }}>
    {children}
  </MercureClientContext.Provider>;
}

export const useMercureClient = () => {
  const context = useContext(MercureClientContext);
  if (!context) {
    throw new Error('useMercureClient must be used within an MercureClientProvider');
  }

  return context;
}

