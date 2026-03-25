import { cva } from 'class-variance-authority';

export const selectInputCva = cva(
  [
    'h-fit w-full min-w-0 cursor-pointer rounded-xs border font-semibold transition-all',
    'border-primary/12 bg-[rgba(26,28,46,0.5)] text-neutral-200 backdrop-blur-md antialiased',
    'appearance-none',
    'focus:border-primary focus:outline-none focus:input-shadow-primary',
    'hover:border-primary/20 hover:text-primary-pastel',
    'disabled:cursor-not-allowed disabled:opacity-50',
  ].join(' '),
  {
    variants: {
      sizing: {
        lg: 'px-5 py-4 pr-12 text-[1.25rem] leading-tight font-title',
        md: 'px-5 py-4 pr-10 text-[0.9rem] leading-normal font-title',
        sm: 'px-3 py-2 pr-9 text-[0.75rem] leading-snug font-title',
      },
      fit: { false: 'w-full', true: 'w-full min-w-[12rem]' },
    },
    defaultVariants: {
      sizing: 'md',
      fit: false,
    },
  }
);
