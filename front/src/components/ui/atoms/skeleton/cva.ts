import { cva } from 'class-variance-authority';

import { typographyVariantSkeletonLineClasses } from '@/components/ui/atoms/typography/typography-size-variants';

export const textSkeletonCva = cva('w-full max-w-full animate-pulse rounded-sm bg-neutral-700/70', {
  variants: {
    variant: typographyVariantSkeletonLineClasses,
  },
  defaultVariants: {
    variant: 'body',
  },
});
