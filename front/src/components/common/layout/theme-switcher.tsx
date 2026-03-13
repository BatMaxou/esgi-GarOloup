'use client';

import { cva } from 'class-variance-authority';
import { motion } from 'motion/react';

import Icon from '@/components/ui/atoms/Icon';
import { ThemeEnum } from '@/utils/enums';
import { useTheme } from '@/contexts/theme-context';

const buttonCva = cva(
  'w-14 rounded-full flex items-center cursor-pointer transition-colors focus:outline-foreground focus:outline-1',
  {
    variants: {
      theme: {
        [ThemeEnum.LIGHT]: 'justify-start bg-neutral-400',
        [ThemeEnum.DARK]: 'justify-end text-light bg-neutral-700',
      },
    },
  }
);

const circleCva = cva('w-6 h-6 m-0.5 rounded-full flex items-center justify-center', {
  variants: {
    theme: {
      [ThemeEnum.LIGHT]: 'bg-light text-secondary',
      [ThemeEnum.DARK]: 'bg-dark text-light',
    },
  },
});

const ThemeSwitcher = () => {
  const { theme, changeTheme } = useTheme();

  return (
    <button type="button" onClick={changeTheme} className={buttonCva({ theme })}>
      <motion.div layout className={circleCva({ theme })}>
        {theme === ThemeEnum.DARK ? <Icon name="moon" className="w-4 h-4" /> : <Icon name="sun" className="w-4 h-4" />}
      </motion.div>
    </button>
  );
};

export default ThemeSwitcher;
