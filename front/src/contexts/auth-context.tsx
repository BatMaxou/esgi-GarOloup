'use client';

import { createContext, ReactNode, useCallback, useContext, useMemo } from 'react';

import type { User } from '@/utils/types';
import { usePathname, useRouter } from '@/i18n/navigation';
import { signIn, signOut, useSession } from '@/lib/auth/auth-client';
import { tempUserSignIn, tempUserSignOut, useTempUserSession } from '@/lib/auth/auth-temp-user-client';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { isLoggedAreaPath, paths } from '@/utils/paths';

type Props = {
  children: ReactNode;
  initialUser?: User | null;
};

type AuthContextType = {
  user: User | null;
  setUser: (user: User | null) => void;
  login: (email: string, password: string) => Promise<boolean>;
  logout: () => Promise<void>;
  getTempUser: (username: string) => Promise<boolean>;
};

export const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: Props) => {
  const pathname = usePathname();
  const router = useRouter();
  const { data: mainSession, refetch: refetchMain } = useSession();
  const { data: tempSession, refetch: refetchTemp } = useTempUserSession();

  const user = useMemo(() => {
    return (mainSession?.user ?? tempSession?.user ?? null) as User | null;
  }, [mainSession?.user, tempSession?.user]);

  const login = useCallback(
    async (email: string, password: string) => {
      const response = await signIn.garoloup({ email, password });
      if (response instanceof ApiClientError) {
        return false;
      }
      // With cookieCache: true, force refetch after login
      await refetchMain();
      return true;
    },
    [refetchMain]
  );

  const logout = useCallback(async () => {
    const shouldLeaveLoggedArea = isLoggedAreaPath(pathname);
    await signOut();
    await tempUserSignOut();
    if (shouldLeaveLoggedArea) {
      router.push(paths.home);
    }
  }, [pathname, router]);

  const getTempUser = useCallback(
    async (username: string) => {
      try {
        const response = await tempUserSignIn.tempUser({ username });
        if (response instanceof ApiClientError) {
          return false;
        }

        await refetchTemp();
        return true;
      } catch {
        return false;
      }
    },
    [refetchTemp]
  );

  return (
    <AuthContext.Provider
      value={{
        user,
        setUser: () => {},
        login,
        logout,
        getTempUser,
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
