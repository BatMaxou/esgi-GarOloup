import cn from 'classnames';
import { cva } from 'class-variance-authority';

export const otpDigitCva = cva(
  cn('shrink-0 text-center rounded-xs border-2 font-title font-bold transition-all outline-none', 'caret-primary'),
  {
    variants: {
      sizing: {
        md: 'size-12 text-xl',
        sm: 'size-10 text-lg',
        lg: 'size-14 text-2xl',
      },
      status: {
        default: cn(
          'bg-secondary/40 border-secondary-pastel/20 text-light',
          'focus:border-primary focus:input-shadow-primary'
        ),
        success: cn('bg-success/15 border-success text-light', 'focus:border-success'),
        error: cn('bg-error/10 border-error text-light', 'focus:border-error'),
        disabled: cn(
          'cursor-not-allowed border-neutral-600 bg-secondary/20 text-neutral-500 opacity-70',
          'focus:border-neutral-600 focus:shadow-none'
        ),
      },
    },
    defaultVariants: {
      sizing: 'md',
      status: 'default',
    },
  }
);
