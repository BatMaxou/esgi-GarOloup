import { cva } from 'class-variance-authority';

export const tabsCva = cva('flex flex-col gap-5', {
  variants: {
    align: {
      left: 'items-start',
      center: 'items-center',
      right: 'items-end',
    },
  },
});
