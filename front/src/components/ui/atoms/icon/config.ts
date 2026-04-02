import { ComponentType, SVGProps } from 'react';
import { MoonStar, Sun, LucideIcon, Github, X } from 'lucide-react';

import Garoloup from '@/assets/icons/garoloup.svg';
import Discord from '@/assets/icons/discord.svg';

export type IconName = keyof typeof raw & string;
export type IconType = LucideIcon | ComponentType<SVGProps<SVGSVGElement>>;
export type Icons = Record<IconName, IconType>;

const raw = {
  garoloup: Garoloup,
  sun: Sun,
  moon: MoonStar,
  github: Github,
  x: X,
  discord: Discord,
};

export const icons: Icons = raw;
export const iconNames = Object.keys(raw) as IconName[];
