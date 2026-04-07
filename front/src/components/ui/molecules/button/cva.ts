import { cva } from 'class-variance-authority';
import cn from 'classnames';

export const buttonCva = cva(
  'h-fit flex items-center justify-center cursor-pointer border-2 transition-all focus:outline-none',
  {
    variants: {
      variant: {
        primary: '',
        secondary: '',
        accent: '',
        neutral: '',
        error: '',
        success: '',
        gradient:
          'bg-linear-(--primary-gradient) bg-origin-border hover:button-shadow-primary focus:button-shadow-primary',
        text: 'bg-transparent hover:underline focus:underline !p-0',
      },
      size: {
        lg: 'px-8 py-4 gap-4',
        md: 'px-6 py-3 gap-3',
        sm: 'px-4 py-2 gap-2',
        xs: 'p-1 gap-1',
      },
      full: { true: 'w-full', false: 'w-fit' },
      disabled: { true: 'cursor-not-allowed opacity-50 hover:bg-transparent focus:bg-transparent' },
      glass: {
        true: 'border-2 backdrop-blur-sm',
        false: 'border-transparent',
      },
      popup: {
        true: 'transition-transform hover:-translate-y-0.5 active:-translate-y-0',
      },
    },
    compoundVariants: [
      { size: 'xs', class: 'rounded-xs' },
      { size: ['sm', 'md', 'lg'], class: 'rounded-sm' },
      {
        variant: 'primary',
        glass: false,
        class: 'bg-primary text-light hover:button-shadow-primary focus:button-shadow-primary',
      },
      {
        variant: 'primary',
        glass: true,
        class: cn(
          'bg-primary-pastel/10 border-primary/80 text-primary',
          'hover:bg-primary-pastel/20 hover:border-primary',
          'focus:bg-primary-pastel/20 focus:border-primary'
        ),
      },
      {
        variant: 'secondary',
        glass: false,
        class: 'bg-secondary text-light hover:button-shadow-secondary focus:button-shadow-secondary',
      },
      {
        variant: 'secondary',
        glass: true,
        class: cn(
          'bg-secondary/40 border-secondary-pastel/20 text-neutral-400',
          'hover:bg-secondary/60 hover:border-secondary-pastel/40',
          'focus:bg-secondary/60 focus:border-secondary-pastel/40'
        ),
      },
      {
        variant: 'accent',
        glass: false,
        class: 'bg-accent text-dark hover:button-shadow-accent focus:button-shadow-accent',
      },
      {
        variant: 'accent',
        glass: true,
        class: cn(
          'bg-accent-pastel/10 border-accent/80 text-accent',
          'hover:bg-accent/20 hover:border-accent',
          'focus:bg-accent/20 focus:border-accent'
        ),
      },
      {
        variant: 'neutral',
        glass: false,
        class: 'bg-foreground text-dark hover:button-shadow-foreground focus:button-shadow-foreground',
      },
      {
        variant: 'neutral',
        glass: true,
        class: cn(
          'bg-foreground/10 border-foreground/20 text-foreground',
          'hover:bg-foreground/20 hover:border-foreground/40',
          'focus:bg-foreground/20 focus:border-foreground/40'
        ),
      },
      {
        variant: 'error',
        glass: false,
        class: 'bg-error text-light hover:button-shadow-error focus:button-shadow-error',
      },
      {
        variant: 'error',
        glass: true,
        class: cn(
          'bg-foreground/10 border-foreground/20 text-foreground',
          'hover:bg-error/10 hover:border-error/40 hover:text-error',
          'focus:bg-error/10 focus:border-error/40 focus:text-error'
        ),
      },
      {
        variant: 'success',
        glass: false,
        class: 'bg-success text-light hover:button-shadow-success focus:button-shadow-success',
      },
      {
        variant: 'success',
        glass: true,
        class: cn(
          'bg-foreground/10 border-foreground/20 text-foreground',
          'hover:bg-success/10 hover:border-success/40 hover:text-success',
          'focus:bg-success/10 focus:border-success/40 focus:text-success'
        ),
      },
    ],
    defaultVariants: {
      variant: 'neutral',
      size: 'md',
      full: false,
      disabled: false,
      glass: false,
      popup: false,
    },
  }
);

export const buttonIconCva = cva('', {
  variants: {
    size: {
      lg: 'w-8 h-8',
      md: 'w-6 h-6',
      sm: 'w-5 h-5',
      xs: 'w-4 h-4',
    },
  },
  defaultVariants: {
    size: 'md',
  },
});
