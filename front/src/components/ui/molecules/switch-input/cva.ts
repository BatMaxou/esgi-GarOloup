import { cva } from 'class-variance-authority';

export const switchTrackCva = cva(
  'relative inline-flex items-center rounded-full border cursor-pointer transition-all focus-within:outline-none',
  {
    variants: {
      variant: {
        primary: '',
        secondary: '',
        accent: '',
        neutral: '',
        error: '',
        success: '',
        gradient: '',
        'day-night': '',
      },
      checked: {
        true: 'justify-end',
        false: 'justify-start',
      },
      size: {
        lg: 'w-16 h-8 p-1',
        md: 'w-14 h-7 p-0.75',
        sm: 'w-12 h-6 p-0.5',
      },
      disabled: {
        true: 'cursor-not-allowed opacity-50',
      },
    },
    compoundVariants: [
      { variant: 'primary', checked: true, class: 'bg-primary/80 border-primary' },
      { variant: 'secondary', checked: true, class: 'bg-secondary/80 border-secondary' },
      { variant: 'accent', checked: true, class: 'bg-accent/80 border-accent' },
      { variant: 'neutral', checked: true, class: 'bg-foreground/80 border-foreground' },
      { variant: 'error', checked: true, class: 'bg-error/80 border-error' },
      { variant: 'success', checked: true, class: 'bg-success/80 border-success' },
      {
        variant: 'gradient',
        checked: true,
        class: 'bg-linear-(--primary-gradient) bg-origin-border border-transparent',
      },
      { variant: 'day-night', checked: true, class: 'bg-neutral-700 border-neutral-700' },
      { variant: 'primary', checked: false, class: 'bg-transparent border-primary' },
      { variant: 'secondary', checked: false, class: 'bg-transparent border-secondary' },
      { variant: 'accent', checked: false, class: 'bg-transparent border-accent' },
      { variant: 'neutral', checked: false, class: 'bg-transparent border-foreground' },
      { variant: 'error', checked: false, class: 'bg-transparent border-error' },
      { variant: 'success', checked: false, class: 'bg-transparent border-success' },
      { variant: 'gradient', checked: false, class: 'bg-transparent border-primary' },
      { variant: 'day-night', checked: false, class: 'bg-neutral-400 border-neutral-400' },
    ],
    defaultVariants: {
      variant: 'primary',
      checked: false,
      size: 'md',
      disabled: false,
    },
  }
);

export const switchThumbCva = cva('relative rounded-full shrink-0', {
  variants: {
    variant: {
      primary: '',
      secondary: '',
      accent: '',
      neutral: '',
      error: '',
      success: '',
      gradient: '',
      'day-night': '',
    },
    checked: {
      true: '',
      false: '',
    },
    size: {
      lg: 'w-6 h-6',
      md: 'w-5 h-5',
      sm: 'w-4 h-4',
    },
  },
  compoundVariants: [
    { variant: ['primary', 'secondary', 'error', 'success', 'gradient'], checked: true, class: 'bg-light' },
    { variant: ['accent', 'neutral'], checked: true, class: 'bg-dark' },
    { variant: 'day-night', checked: true, class: 'bg-dark text-light' },
    { variant: 'primary', checked: false, class: 'bg-primary' },
    { variant: 'secondary', checked: false, class: 'bg-secondary' },
    { variant: 'accent', checked: false, class: 'bg-accent' },
    { variant: 'neutral', checked: false, class: 'bg-foreground' },
    { variant: 'error', checked: false, class: 'bg-error' },
    { variant: 'success', checked: false, class: 'bg-success' },
    { variant: 'gradient', checked: false, class: 'bg-linear-(--primary-gradient)' },
    { variant: 'day-night', checked: false, class: 'bg-light text-dark' },
  ],
  defaultVariants: {
    variant: 'primary',
    checked: false,
    size: 'md',
  },
});

export const switchIconCva = cva('', {
  variants: {
    size: {
      lg: 'w-4 h-4',
      md: 'w-3 h-3',
      sm: 'w-2.5 h-2.5',
    },
  },
  defaultVariants: {
    size: 'md',
  },
});
