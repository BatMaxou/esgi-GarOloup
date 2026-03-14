import { cva } from 'class-variance-authority';

export const typographyCva = cva('antialiased', {
  variants: {
    variant: {
      body: 'text-[1rem] leading-[1.5rem]',
      'body-sm': 'text-[0.75rem] leading-[1.25rem]',
      'body-xs': 'text-[0.5rem] leading-[1rem]',
      'heading-1': 'text-[2.5rem] leading-[3rem] xs:text-[4rem] xs:leading-[4.5rem] sm:text-[6.5rem] sm:leading-[7rem]',
      'heading-2': 'text-[2rem] leading-[2.5rem] sm:text-[4rem] sm:leading-[4.5rem]',
      'heading-3': 'text-[1.5rem] leading-[2rem] sm:text-[2rem] sm:leading-[2.5rem]',
      subtitle: 'text-[1.25rem] leading-[1.75rem]',
      button: 'text-[1rem] leading-[1.5rem]',
      controlled: '',
    },
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
    },
    bold: { true: 'font-bold' },
    center: { true: 'text-center' },
    underline: { true: 'underline' },
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
