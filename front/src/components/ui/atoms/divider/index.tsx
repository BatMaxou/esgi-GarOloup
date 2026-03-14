'use client';

import type { VariantProps } from 'class-variance-authority';

import { dividerCva } from './cva';

type Props = VariantProps<typeof dividerCva> & {
  className?: string;
};

const Divider = ({ variant, orientation, className }: Props) => {
  return <span className={dividerCva({ variant, orientation, className })} />;
};

export default Divider;
