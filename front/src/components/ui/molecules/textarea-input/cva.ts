import cn from 'classnames';
import { cva } from 'class-variance-authority';

export const textareaInputCva = cva(
  cn(
    'block w-full min-h-0 resize-y rounded-xs border-2 transition-all',
    'bg-[rgba(26,28,46,0.5)] border-primary/12 text-light',
    'caret-primary',
    'focus:border-primary focus:outline-none focus:input-shadow-primary',
    'disabled:cursor-not-allowed disabled:opacity-50',
    'placeholder:text-neutral-500'
  ),
  {
    variants: {
      sizing: {
        lg: 'min-h-[8rem] p-4 text-[1rem] leading-[1.75rem] font-title uppercase',
        md: 'min-h-[6.5rem] p-3 text-[1rem] leading-[1.5rem] font-title',
        sm: 'min-h-[4.5rem] p-1.5 text-[0.75rem] leading-[1.25rem]',
      },
      fit: { false: 'w-full' },
    },
    defaultVariants: {
      sizing: 'md',
      fit: false,
    },
  }
);
