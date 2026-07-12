import { cva } from 'class-variance-authority';

export const dotCva = cva('shrink-0 rounded-full', {
  variants: {
    variant: {
      default: 'size-2 bg-primary',
      live: 'size-2 animate-pulse bg-success shadow-[0_0_6px] shadow-success',
      inactive: 'size-2 animate-pulse bg-error shadow-[0_0_6px] shadow-error',
    },
  },
  defaultVariants: {
    variant: 'default',
  },
});
