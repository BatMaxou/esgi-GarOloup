'use client';

import type { VariantProps } from 'class-variance-authority';

import { dotCva } from './cva';

type Props = VariantProps<typeof dotCva> & {
  className?: string;
  decorative?: boolean;
};

const Dot = ({ variant, className, decorative = true }: Props) => {
  return <span className={dotCva({ variant, className })} aria-hidden={decorative ? true : undefined} />;
};

export default Dot;
