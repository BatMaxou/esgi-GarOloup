import { GameTeamEnum, GameRuntimeStepEnum } from '@/utils/enums';
import type { IconName } from '@/components/ui/atoms/icon/config';
import type { EventColor } from './types';

export const teamCardClasses: Record<GameTeamEnum, string> = {
  [GameTeamEnum.VILLAGE]:
    'border-green-500/30 shadow-[0_8px_30px_oklch(from_var(--color-green-500)_l_c_h_/_0.12)] before:bg-[radial-gradient(circle_at_50%_0%,oklch(from_var(--color-green-500)_l_c_h_/_0.16),transparent_70%)]',
  [GameTeamEnum.WEREWOLF]:
    'border-red-500/30 shadow-[0_8px_30px_oklch(from_var(--color-red-500)_l_c_h_/_0.12)] before:bg-[radial-gradient(circle_at_50%_0%,oklch(from_var(--color-red-500)_l_c_h_/_0.16),transparent_70%)]',
  [GameTeamEnum.SOLO]:
    'border-yellow-500/30 shadow-[0_8px_30px_oklch(from_var(--color-yellow-500)_l_c_h_/_0.12)] before:bg-[radial-gradient(circle_at_50%_0%,oklch(from_var(--color-yellow-500)_l_c_h_/_0.16),transparent_70%)]',
  [GameTeamEnum.COUPLE]:
    'border-pink-500/30 shadow-[0_8px_30px_oklch(from_var(--color-pink-500)_l_c_h_/_0.12)] before:bg-[radial-gradient(circle_at_50%_0%,oklch(from_var(--color-pink-500)_l_c_h_/_0.16),transparent_70%)]',
};

export const teamColorClass: Record<GameTeamEnum, string> = {
  [GameTeamEnum.VILLAGE]: 'text-green-500',
  [GameTeamEnum.WEREWOLF]: 'text-red-500',
  [GameTeamEnum.SOLO]: 'text-yellow-500',
  [GameTeamEnum.COUPLE]: 'text-pink-500',
};

export const roleIcon: Record<string, IconName> = {
  villager: 'villager',
  werewolf: 'werewolf',
  seer: 'seer',
  witch: 'witch',
  wild_child: 'wild-child',
  hunter: 'hunter',
  infect_father: 'infect-father',
  cupidon: 'cupidon',
  assassin: 'assassin',
};

export const periodTypeIcon = {
  [GameRuntimeStepEnum.NIGHT]: 'moon',
  [GameRuntimeStepEnum.DAY]: 'sun',
  [GameRuntimeStepEnum.VOTE]: 'users',
} as const;

export const eventCardClasses: Record<EventColor, string> = {
  red: 'border-red-500/30 shadow-[0_8px_30px_oklch(from_var(--color-red-500)_l_c_h_/_0.12)] before:bg-[radial-gradient(circle_at_50%_0%,oklch(from_var(--color-red-500)_l_c_h_/_0.16),transparent_70%)]',
  green:
    'border-green-500/30 shadow-[0_8px_30px_oklch(from_var(--color-green-500)_l_c_h_/_0.12)] before:bg-[radial-gradient(circle_at_50%_0%,oklch(from_var(--color-green-500)_l_c_h_/_0.16),transparent_70%)]',
  blue: 'border-blue-500/30 shadow-[0_8px_30px_oklch(from_var(--color-blue-500)_l_c_h_/_0.12)] before:bg-[radial-gradient(circle_at_50%_0%,oklch(from_var(--color-blue-500)_l_c_h_/_0.16),transparent_70%)]',
  neutral: 'border-primary/15',
};

export const eventIconColor: Record<EventColor, string> = {
  red: 'text-red-500',
  green: 'text-green-500',
  blue: 'text-blue-500',
  neutral: 'text-neutral-500',
};

export const actionEventMeta: Record<string, { icon: IconName; color: EventColor }> = {
  werewolfMurder: { icon: 'werewolf', color: 'red' },
  witchPoison: { icon: 'poison', color: 'red' },
  assassinMurder: { icon: 'skull', color: 'red' },
  infection: { icon: 'werewolf', color: 'red' },
  witchSave: { icon: 'heal', color: 'green' },
  seerReveal: { icon: 'seer', color: 'blue' },
};
