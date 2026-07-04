import { ComponentType, SVGProps } from 'react';
import { MoonStar, Sun, LucideIcon, Github, X, Plus, Minus, Check, Users } from 'lucide-react';

import Garoloup from '@/assets/icons/garoloup.svg';
import Discord from '@/assets/icons/discord.svg';
import Crown from '@/assets/icons/crown.svg';
import Timer from '@/assets/icons/timer.svg';
import Copy from '@/assets/icons/copy.svg';
import Skull from '@/assets/icons/skull.svg';
import Werewolf from '@/assets/icons/werewolf.svg';
import Poison from '@/assets/icons/poison.svg';
import Witch from '@/assets/icons/witch.svg';
import Heal from '@/assets/icons/heal.svg';
import Seer from '@/assets/icons/seer.svg';
import TargetEye from '@/assets/icons/target-eye.svg';
import HelpCircle from '@/assets/icons/help-circle.svg';
import Cancel from '@/assets/icons/cancel.svg';

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
  plus: Plus,
  minus: Minus,
  crown: Crown,
  timer: Timer,
  copy: Copy,
  check: Check,
  skull: Skull,
  werewolf: Werewolf,
  poison: Poison,
  witch: Witch,
  heal: Heal,
  seer: Seer,
  targetEye: TargetEye,
  questionMark: HelpCircle,
  cancel: Cancel,
  users: Users,
};

export const icons: Icons = raw;
export const iconNames = Object.keys(raw) as IconName[];
