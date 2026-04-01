'use client';

import classNames from 'classnames';
import type { VariantProps } from 'class-variance-authority';

import { textSkeletonCva } from './cva';

type Props = VariantProps<typeof textSkeletonCva> & {
  className?: string;
  lines?: number;
  lineClassName?: string;
};

const TextSkeleton = ({ variant, className, lines = 1, lineClassName }: Props) => {
  return (
    <div className={classNames('flex w-full flex-col gap-1', className)} aria-hidden>
      {Array.from({ length: lines }, (_, i) => (
        <div key={i} className={classNames(textSkeletonCva({ variant }), lineClassName)} />
      ))}
    </div>
  );
};

export default TextSkeleton;
