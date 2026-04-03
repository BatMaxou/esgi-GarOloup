'use client';

import type { VariantProps } from 'class-variance-authority';

import { tagCva } from './cva';
import Typography from '@/components/ui/atoms/typography';

export type TagVariantType = VariantProps<typeof tagCva>['variant'];

type Props = VariantProps<typeof tagCva> & {
  label?: string;
  lowerCase?: boolean;
  className?: string;
};

const Tag = ({ label, lowerCase, className, variant, size, active }: Props) => {
  return (
    <span className={tagCva({ variant, size, active, className })}>
      <Typography variant="tag" textColor="controlled" uppercase={!lowerCase} bold center>
        {label}
      </Typography>
    </span>
  );
};

export default Tag;
