'use client';

import { createContext, ReactNode, useCallback, useContext, useMemo, useState } from 'react';

import type { User } from '@/utils/types';
import { usePathname, useRouter } from '@/i18n/navigation';
import { signIn, signOut, useSession } from '@/lib/auth/auth-client';
import { tempUserSignIn, tempUserSignOut, useTempUserSession } from '@/lib/auth/auth-temp-user-client';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { isLoggedAreaPath, paths } from '@/utils/paths';
import { toast } from 'react-toastify';
import { useTranslations } from 'next-intl';

type Props = {
  children: ReactNode;
  initialUser?: User | null;
};

type AuthContextType = {
  user: User | null;
  isLoginLoading: boolean;
  isTempUserLoginLoading: boolean;
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
  const t = useTranslations('contexts.auth');
  const [isLoginLoading, setIsLoginLoading] = useState(false);
  const [isTempUserLoginLoading, setIsTempUserLoginLoading] = useState(false);

  const user = useMemo(() => {
    return (mainSession?.user ?? tempSession?.user ?? null) as User | null;
  }, [mainSession?.user, tempSession?.user]);

  const login = useCallback(
    async (email: string, password: string) => {
      setIsLoginLoading(true);
      const response = await signIn.garoloup({ email, password });

      if (response.error) {
        if (response.error.status === 401) {
          toast.error(t('invalidCredentials'));
        } else {
          toast.error(t('loginError'));
        }
        setIsLoginLoading(false);
        return false;
      }
      // With cookieCache: true, force refetch after login
      await refetchMain();
      setIsLoginLoading(false);
      return true;
    },
    [refetchMain, t]
  );

  const logout = useCallback(async () => {
    setIsLoginLoading(true);
    const shouldLeaveLoggedArea = isLoggedAreaPath(pathname);
    await signOut();
    await tempUserSignOut();
    if (shouldLeaveLoggedArea) {
      router.push(paths.home);
    }
    setIsLoginLoading(false);
  }, [pathname, router]);

  const getTempUser = useCallback(
    async (username: string) => {
      setIsTempUserLoginLoading(true);
      try {
        const response = await tempUserSignIn.tempUser({ username });
        if (response instanceof ApiClientError) {
          setIsTempUserLoginLoading(false);
          return false;
        }

        await refetchTemp();
        setIsTempUserLoginLoading(false);
        return true;
      } catch {
        setIsTempUserLoginLoading(false);
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
        isLoginLoading,
        isTempUserLoginLoading,
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
