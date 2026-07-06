import { cva } from 'class-variance-authority';

export const dialogBackdropCva = cva('absolute inset-0 bg-[rgba(26,28,46,0.72)] backdrop-blur-md transition-opacity');

export const dialogPanelCva = cva(
  [
    'relative z-10 flex max-h-[min(90dvh,800px)] w-full flex-col gap-4 overflow-y-auto rounded-sm border border-primary/15',
    'bg-[rgba(26,28,46,0.85)] p-6 shadow-(--shadow) backdrop-blur-[16px]',
    'outline-none',
  ].join(' '),
  {
    variants: {
      size: {
        sm: 'max-w-sm',
        md: 'max-w-lg',
        lg: 'max-w-2xl',
        xl: 'max-w-4xl',
      },
    },
    defaultVariants: {
      size: 'md',
    },
  }
);
