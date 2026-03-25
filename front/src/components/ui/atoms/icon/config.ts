import { ComponentType, SVGProps } from 'react';
import { MoonStar, Sun, LucideIcon, Github, X } from 'lucide-react';

import Garoloup from '@/assets/icons/garoloup.svg';
import Discord from '@/assets/icons/discord.svg';

type IconType = LucideIcon | ComponentType<SVGProps<SVGSVGElement>>;

export const icons: { [key: string]: IconType } = {
  garoloup: Garoloup,
  sun: Sun,
  moon: MoonStar,
  github: Github,
  x: X,
  discord: Discord,
};
