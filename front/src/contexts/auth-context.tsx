'use client';

import { createContext, ReactNode, useCallback, useContext, useMemo } from 'react';

import { User } from '@/utils/types';
import { signIn, signOut, useSession } from '@/lib/auth/auth-client';

type Props = {
  children: ReactNode;
  initialUser?: User | null;
};

type AuthContextType = {
  user: User | null;
  setUser: (user: User | null) => void;
  login: (email: string, password: string) => void;
  logout: () => void;
};

export const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: Props) => {
  const { data: session, refetch } = useSession();

  const user = useMemo(() => {
    return session?.user ?? null;
  }, [session])

  const login = useCallback(async (email: string, password: string) => {
    await signIn.garoloup({ email, password });
    // With cookieCache: true, force refetch after login
    await refetch();
  }, [refetch]);

  const logout = useCallback(async () => {
    await signOut();
  }, []);

  return (
    <AuthContext.Provider
      value={{
        user,
        setUser: () => {},
        login,
        logout,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }

  return context;
};
