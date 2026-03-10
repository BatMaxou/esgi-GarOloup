'use client';

import { ThemeEnum } from '@/utils/enums';
import { createContext, ReactNode, useCallback, useContext, useEffect } from 'react';
import { useLocalStorage } from 'react-use';

type Props = {
  children: ReactNode;
};

type ThemeContextType = {
  theme?: ThemeEnum;
  changeTheme: () => void;
};

export const ThemeContext = createContext<ThemeContextType | undefined>(undefined);

export const ThemeProvider = ({ children }: Props) => {
  const [theme, setTheme] = useLocalStorage('theme', ThemeEnum.DARK);

  const changeTheme = useCallback(() => {
    setTheme(theme === ThemeEnum.DARK ? ThemeEnum.LIGHT : ThemeEnum.DARK);
  }, [theme, setTheme]);

  useEffect(() => {
    if (theme) {
      document.documentElement.setAttribute('data-theme', theme);
    }
  }, [theme]);

  return <ThemeContext.Provider value={{ theme, changeTheme }}>{children}</ThemeContext.Provider>;
};

export const useTheme = () => {
  const context = useContext(ThemeContext);
  if (!context) {
    throw new Error('useTheme must be used within a ThemeProvider');
  }

  return context;
};
