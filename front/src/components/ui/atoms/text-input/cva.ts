import cn from 'classnames';
import { cva } from 'class-variance-authority';

export const textInputCva = cva(
  cn(
    'h-fit flex items-center cursor-text rounded-xs border-2 transition-all',
    'bg-secondary/40 border-secondary-pastel/20 text-light',
    'cursor-text caret-primary',
    'focus:outline-none focus:input-shadow-primary focus:border-primary'
  ),
  {
    variants: {
      sizing: {
        lg: 'p-4 text-[2rem] leading-[1.75rem] font-special uppercase',
        md: 'p-4 text-[1rem] leading-[1.5rem]',
        sm: 'p-2 text-[0.75rem] leading-[1.25rem]',
      },
      fit: { false: 'w-full' },
    },
    compoundVariants: [],
    defaultVariants: {
      sizing: 'md',
      fit: false,
    },
  }
);
