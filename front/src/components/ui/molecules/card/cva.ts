import { cva } from 'class-variance-authority';

const roleShell = [
  'group/role',
  'before:pointer-events-none before:absolute before:inset-0 before:rounded-sm before:opacity-0',
  'before:transition-opacity before:duration-[250ms]',
  'hover:border-primary/35 hover:shadow-[0_12px_40px_rgba(0,0,0,0.4)] hover:before:opacity-100',
].join(' ');

const defaultFeatured =
  '!border-accent/30 bg-[rgba(236,167,44,0.06)] hover:border-accent/50 hover:shadow-lg hover:shadow-black/25';

export const cardCva = cva(
  [
    'relative flex cursor-default overflow-hidden rounded-sm border border-primary/15',
    'bg-[rgba(26,28,46,0.6)] px-5 py-6 backdrop-blur-[16px]',
    'transition-all duration-[250ms] ease-out',
  ].join(' '),
  {
    variants: {
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
        role: roleShell,
      },
      roleVariant: {
        none: '',
        loup: 'before:bg-[radial-gradient(circle_at_50%_0%,rgba(211,62,54,0.12),transparent_70%)]',
        sorciere: 'before:bg-[radial-gradient(circle_at_50%_0%,rgba(159,154,214,0.1),transparent_70%)]',
        villageois: 'before:bg-[radial-gradient(circle_at_50%_0%,rgba(50,186,85,0.08),transparent_70%)]',
        gm: 'before:bg-[radial-gradient(circle_at_50%_0%,rgba(236,167,44,0.12),transparent_70%)]',
      },
      emphasis: {
        base: '',
        accent: '',
        success: '',
        error: '',
      },
    },
    compoundVariants: [
      {
        type: 'default',
        emphasis: 'base',
        class: 'hover:border-primary/30 hover:shadow-lg hover:shadow-black/25',
      },
      {
        type: 'default',
        emphasis: 'accent',
        class: defaultFeatured,
      },
      {
        type: 'default',
        emphasis: 'success',
        class: '!border-success/15 hover:!border-success/40 hover:shadow-lg hover:shadow-black/25',
      },
      {
        type: 'default',
        emphasis: 'error',
        class: '!border-error/20 hover:!border-error/45 hover:shadow-lg hover:shadow-black/25',
      },
    ],
    defaultVariants: {
      orientation: 'vertical',
      liftOnHover: true,
      type: 'default',
      roleVariant: 'none',
      emphasis: 'base',
    },
  }
);
