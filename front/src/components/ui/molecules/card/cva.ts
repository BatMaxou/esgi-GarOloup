import { cva } from 'class-variance-authority';

import { headerCardEmphasisClasses } from '@/components/common/layout/header-surface';

const roleShellBase = [
  'group/role',
  'before:pointer-events-none before:absolute before:inset-0 before:rounded-sm before:opacity-0',
  'before:transition-opacity before:duration-[250ms]',
].join(' ');

const roleShellHover = 'hover:border-primary/35 hover:shadow-[0_12px_40px_rgba(0,0,0,0.4)] hover:before:opacity-100';

const defaultAccentBase = 'border-accent/30 bg-accent/5';
const defaultAccentHover = 'hover:border-accent/50 hover:shadow-lg hover:shadow-black/25';

const playerCurrentBase = '!bg-secondary/20 border-primary/20 shadow-none';
const playerDefaultBase = 'border-transparent !bg-transparent shadow-none';
const playerHover = 'hover:!border-primary/20 hover:!bg-primary/10';

export const cardCva = cva(
  [
    'relative flex cursor-default rounded-sm border border-primary/15',
    'bg-[rgba(26,28,46,0.85)]',
    'transition-all duration-[250ms] ease-out',
  ].join(' '),
  {
    variants: {
      hoverable: {
        true: '',
        false: '',
      },
      fullfilled: {
        true: 'p-0',
        false: 'px-5 py-6',
      },
      orientation: {
        vertical: 'flex-col',
        horizontal: 'flex-row',
      },
      liftOnHover: {
        true: 'hover:-translate-y-1',
        false: '',
      },
      type: {
        default: '',
        role: roleShellBase,
      },
      roleVariant: {
        none: '',
        werewolf: 'before:bg-[radial-gradient(circle_at_50%_0%,rgba(211,62,54,0.2),transparent_70%)]',
        village: 'before:bg-[radial-gradient(circle_at_50%_0%,rgba(50,186,85,0.2),transparent_70%)]',
        solo: 'before:bg-[radial-gradient(circle_at_50%_0%,rgba(0,133,161,0.2),transparent_70%)]',
        couple: 'before:bg-[radial-gradient(circle_at_50%_0%,rgba(255,153,221,0.2),transparent_70%)]',
      },
      emphasis: {
        base: '',
        accent: '',
        success: '',
        error: '',
        gradient: 'bg-primary-pastel/20 border-primary/50',
        likeHeader: headerCardEmphasisClasses,
        player: '',
      },
      playerHighlight: {
        current: '',
        default: '',
      },
    },
    compoundVariants: [
      {
        type: 'role',
        hoverable: true,
        class: roleShellHover,
      },
      {
        type: 'default',
        emphasis: 'base',
        hoverable: true,
        class: 'hover:border-primary/30 hover:shadow-lg hover:shadow-black/25',
      },
      {
        type: 'default',
        emphasis: 'accent',
        class: defaultAccentBase,
      },
      {
        type: 'default',
        emphasis: 'accent',
        hoverable: true,
        class: defaultAccentHover,
      },
      {
        type: 'default',
        emphasis: 'success',
        class: '!border-success/15',
      },
      {
        type: 'default',
        emphasis: 'success',
        hoverable: true,
        class: 'hover:!border-success/40 hover:shadow-lg hover:shadow-black/25',
      },
      {
        type: 'default',
        emphasis: 'error',
        class: '!border-error/20',
      },
      {
        type: 'default',
        emphasis: 'error',
        hoverable: true,
        class: 'hover:!border-error/45 hover:shadow-lg hover:shadow-black/25',
      },
      {
        type: 'default',
        emphasis: 'player',
        playerHighlight: 'current',
        class: playerCurrentBase,
      },
      {
        type: 'default',
        emphasis: 'player',
        playerHighlight: 'default',
        class: playerDefaultBase,
      },
      {
        type: 'default',
        emphasis: 'player',
        hoverable: true,
        class: playerHover,
      },
    ],
    defaultVariants: {
      hoverable: true,
      orientation: 'vertical',
      liftOnHover: true,
      type: 'default',
      roleVariant: 'none',
      emphasis: 'base',
      playerHighlight: 'default',
    },
  }
);
