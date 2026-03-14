import { ComponentType, SVGProps } from 'react';
import { MoonStar, Sun, LucideIcon } from 'lucide-react';

import Garoloup from '@/assets/icons/garoloup.svg';

type IconType = LucideIcon | ComponentType<SVGProps<SVGSVGElement>>;

export const icons: { [key: string]: IconType } = {
  garoloup: Garoloup,
  sun: Sun,
  moon: MoonStar,
};
