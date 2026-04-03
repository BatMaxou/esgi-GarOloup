'use client';

import type { VariantProps } from 'class-variance-authority';

import { tagCva, tagIconCva } from './cva';
import Typography from '@/components/ui/atoms/typography';
import { IconName } from '../../atoms/icon/config';
import Icon from '../../atoms/icon';

type Props = VariantProps<typeof tagCva> & {
  label?: string;
  lowerCase?: boolean;
  className?: string;
  leftIcon?: IconName;
  rightIcon?: IconName;
};

const Tag = ({ label, lowerCase, className, variant, size, leftIcon, rightIcon }: Props) => {
  return (
    <span className={tagCva({ variant, size, className })}>
      {leftIcon && (
        <Typography variant="tag" textColor="controlled">
          <Icon name={leftIcon} className={tagIconCva({ size })} />
        </Typography>
      )}
      <Typography variant="tag" textColor="controlled" uppercase={!lowerCase} bold center>
        {label}
      </Typography>
      {rightIcon && (
        <Typography variant="tag" textColor="controlled">
          <Icon name={rightIcon} className={tagIconCva({ size })} />
        </Typography>
      )}
    </span>
  );
};

export default Tag;
