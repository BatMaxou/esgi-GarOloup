import { cva } from 'class-variance-authority';

export const dividerCva = cva('block', {
  variants: {
    variant: {
      neutral: '',
      primary: '',
      secondary: '',
      accent: '',
      success: '',
      error: '',
    },
    orientation: {
      horizontal: 'h-px',
      vertical: 'w-px',
    },
  },
  compoundVariants: [
    {
      variant: 'neutral',
      orientation: 'horizontal',
      class: 'divider-horizontal-light',
    },
    {
      variant: 'neutral',
      orientation: 'vertical',
      class: 'divider-vertical-light',
    },
    {
      variant: 'primary',
      orientation: 'horizontal',
      class: 'divider-horizontal-primary',
    },
    {
      variant: 'primary',
      orientation: 'vertical',
      class: 'divider-vertical-primary',
    },
    {
      variant: 'secondary',
      orientation: 'horizontal',
      class: 'divider-horizontal-secondary',
    },
    {
      variant: 'secondary',
      orientation: 'vertical',
      class: 'divider-vertical-secondary',
    },
    {
      variant: 'accent',
      orientation: 'horizontal',
      class: 'divider-horizontal-accent',
    },
    {
      variant: 'accent',
      orientation: 'vertical',
      class: 'divider-vertical-accent',
    },
    {
      variant: 'success',
      orientation: 'horizontal',
      class: 'divider-horizontal-success',
    },
    {
      variant: 'success',
      orientation: 'vertical',
      class: 'divider-vertical-success',
    },
    {
      variant: 'error',
      orientation: 'horizontal',
      class: 'divider-horizontal-error',
    },
    {
      variant: 'error',
      orientation: 'vertical',
      class: 'divider-vertical-error',
    },
  ],
  defaultVariants: {
    variant: 'neutral',
    orientation: 'horizontal',
  },
});
