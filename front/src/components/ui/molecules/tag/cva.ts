import { cva } from 'class-variance-authority';

export const tagCva = cva('h-fit w-fit flex items-center justify-center rounded-xs border transition-all', {
  variants: {
    variant: {
      primary: 'bg-primary-pastel/20 border-primary text-primary',
      secondary: 'bg-secondary/60 border-secondary-pastel/40 text-neutral-400',
      accent: 'bg-accent/20 border-accent text-accent',
      neutral: 'bg-foreground/20 border-foreground/40 text-foreground',
      error: 'bg-error/10 border-error/40 text-error',
      success: 'bg-success/10 border-success/40 text-success',
    },
    size: {
      lg: 'px-3 py-1 gap-1.5',
      md: 'px-2 gap-1',
    },
  },
  defaultVariants: {
    variant: 'neutral',
    size: 'md',
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
