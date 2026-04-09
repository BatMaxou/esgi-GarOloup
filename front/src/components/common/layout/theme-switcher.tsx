'use client';

import SwitchInput from '@/components/ui/molecules/switch-input';
import { ThemeEnum } from '@/utils/enums';
import { useTheme } from '@/contexts/theme-context';

const ThemeSwitcher = () => {
  const { theme, changeTheme } = useTheme();

  return (
    <SwitchInput
      variant="day-night"
      size="lg"
      checked={theme === ThemeEnum.DARK}
      checkedIcon="moon"
      uncheckedIcon="sun"
      onChange={changeTheme}
    />
  );
};

export default ThemeSwitcher;
