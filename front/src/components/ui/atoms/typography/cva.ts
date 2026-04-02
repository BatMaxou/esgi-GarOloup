import { cva } from 'class-variance-authority';

import { typographyVariantTextClasses } from './typography-size-variants';

export const typographyCva = cva('antialiased', {
  variants: {
    variant: typographyVariantTextClasses,
    textColor: {
      controlled: 'text-inherit',
      text: 'text-foreground',
      light: 'text-light',
      dark: 'text-dark',
      primary: 'text-primary',
      secondary: 'text-secondary',
      accent: 'text-accent',
      error: 'text-error',
      success: 'text-success',
      'neutral-200': 'text-neutral-200',
      'neutral-300': 'text-neutral-300',
      'neutral-400': 'text-neutral-400',
      'neutral-500': 'text-neutral-500',
      'neutral-600': 'text-neutral-600',
      'neutral-700': 'text-neutral-700',
      'neutral-800': 'text-neutral-800',
    },
    bold: { true: 'font-bold' },
    center: { true: 'text-center' },
    underline: { true: 'underline' },
    uppercase: { true: 'uppercase' },
    ellipsis: { true: 'truncate' },
    special: { true: 'font-special' },
  },
  compoundVariants: [
    {
      special: false,
      variant: ['heading-1', 'heading-2', 'heading-3', 'subtitle'],
      class: 'font-title',
    },
    {
      special: false,
      variant: ['body', 'body-sm', 'body-xs', 'controlled', 'button'],
      class: 'font-normal',
    },
  ],
  defaultVariants: {
    variant: 'body',
    textColor: 'text',
    bold: false,
    center: false,
    underline: false,
    ellipsis: false,
    special: false,
  },
});
