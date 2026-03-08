'use client';

import { cva } from 'class-variance-authority';
import { useCallback, useEffect, useState } from 'react';
import { motion } from 'motion/react';

import Icon from '@/components/ui/atoms/Icon';
import { ThemeEnum } from '@/utils/enums';

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
      [ThemeEnum.LIGHT]: 'bg-light',
      [ThemeEnum.DARK]: 'bg-dark',
    },
  },
});

const ThemeSwitcher = () => {
  const [theme, setTheme] = useState<ThemeEnum>(ThemeEnum.DARK);

  const handleChange = useCallback(() => {
    setTheme((theme) => (theme === ThemeEnum.DARK ? ThemeEnum.LIGHT : ThemeEnum.DARK));
  }, []);

  useEffect(() => {
    if (!document) {
      return;
    }

    document.documentElement.setAttribute('data-theme', theme);
  }, [theme]);

  return (
    <button type="button" onClick={handleChange} className={buttonCva({ theme })}>
      <motion.div layout className={circleCva({ theme })}>
        {theme === ThemeEnum.DARK ? <Icon name="moon" className="w-4 h-4" /> : <Icon name="sun" className="w-4 h-4" />}
      </motion.div>
    </button>
  );
};

export default ThemeSwitcher;
