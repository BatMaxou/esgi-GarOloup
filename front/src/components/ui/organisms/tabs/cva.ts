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

export const itemCva = cva('px-6 py-3 cursor-pointer transition-all item-follow-anchor hover:opacity-100', {
  variants: {
    active: {
      true: 'item-follow-anchor-active',
      false: 'opacity-70',
    },
  },
});
