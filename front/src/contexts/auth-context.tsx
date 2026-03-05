'use client';

import {
  createContext,
  ReactNode,
  useCallback,
  useContext,
  useEffect,
  useState,
} from 'react';

import { TempUser, User } from '@/utils/types';
import { useApiClient } from '@/contexts/api-context';
import { ApiClientError } from '@/lib/api/ApiClientError';

type Props = {
  children: ReactNode;
};

type AuthContextType = {
  user: User | null;
  setUser: (user: User | null) => void;
  tempUser: TempUser | null;
  setTempUser: (user: User | null) => void;
  logout: () => void;
};

export const AuthContext = createContext<AuthContextType | undefined>(
  undefined
);

export const AuthProvider = ({ children }: Props) => {
  const [user, setUser] = useState<User | null>(null);
  const [tempUser, setTempUser] = useState<TempUser | null>(null);
  const { apiClient } = useApiClient();

  const logout = useCallback(() => {
    setUser(null);
    setTempUser(null);
  }, []);

  useEffect(() => {
    if (apiClient.token) {
      apiClient.me.get().then((maybeUser) => {
        if (!(maybeUser instanceof ApiClientError)) {
          setUser(maybeUser);
          setTempUser(null);
        }
      });
    }
  }, [apiClient]);

  return (
    <AuthContext.Provider
      value={{
        user,
        setUser,
        tempUser,
        setTempUser,
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
