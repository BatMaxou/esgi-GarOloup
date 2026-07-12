import { cva } from 'class-variance-authority';

export const tagCva = cva('h-fit w-fit flex items-center justify-center rounded-xs border transition-all', {
  variants: {
    variant: {
      primary: 'border-primary text-primary',
      secondary: 'border-secondary-pastel/40 text-neutral-400',
      accent: 'border-accent text-accent',
      neutral: 'border-foreground/40 text-foreground',
      error: 'border-error/40 text-error',
      success: 'border-success/40 text-success',
      couple: 'border-pink-500/40 text-pink-500',
    },
    size: {
      lg: 'px-3 py-1 gap-1.5',
      md: 'px-2 gap-1',
    },
    active: {
      true: '',
      false: '',
    },
    disabled: {
      true: 'opacity-50 pointer-events-none',
      false: '',
    },
  },
  compoundVariants: [
    { variant: 'primary', active: false, class: 'bg-primary-pastel/20' },
    { variant: 'primary', active: true, class: 'bg-primary/12' },
    { variant: 'secondary', active: false, class: 'bg-secondary/40' },
    { variant: 'secondary', active: true, class: 'bg-secondary-pastel/28' },
    { variant: 'accent', active: false, class: 'bg-accent/20' },
    { variant: 'accent', active: true, class: 'bg-accent/12' },
    { variant: 'neutral', active: false, class: 'bg-foreground/20' },
    { variant: 'neutral', active: true, class: 'bg-foreground/28' },
    { variant: 'error', active: false, class: 'bg-error/20' },
    { variant: 'error', active: true, class: 'bg-error/28' },
    { variant: 'success', active: false, class: 'bg-success/10' },
    { variant: 'success', active: true, class: 'bg-success/28' },
    { variant: 'couple', active: false, class: 'bg-pink-500/20' },
    { variant: 'couple', active: true, class: 'bg-pink-500/28' },
  ],
  defaultVariants: {
    variant: 'neutral',
    size: 'md',
    active: false,
    disabled: false,
  },
});

export const tagIconCva = cva('', {
  variants: {
    size: {
      lg: 'w-4 h-4',
      md: 'w-3 h-3',
    },
  },
  defaultVariants: {
    size: 'md',
  },
});
