'use client';

import type { VariantProps } from 'class-variance-authority';

import { buttonCva } from './cva';
import Typography from '@/components/ui/atoms/typography';

type Tags = 'a' | 'button';

type Props = VariantProps<typeof buttonCva> & {
  label?: string;
  className?: string;
  asLink?: boolean;
  href?: string;
  onClick?: () => void;
};

const Button = ({ label, className, asLink, variant, size, full, disabled, glass, popup, ...props }: Props) => {
  const Tag: Tags = asLink ? 'a' : 'button';

  return (
    <Tag className={buttonCva({ variant, size, full, disabled, glass, popup, className })} {...props}>
      <Typography variant="button" textColor="controlled" bold center>
        {label}
      </Typography>
    </Tag>
  );
};

export default Button;
