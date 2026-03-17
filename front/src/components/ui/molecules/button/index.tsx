'use client';

import type { VariantProps } from 'class-variance-authority';

import { buttonCva } from './cva';
import Typography from '@/components/ui/atoms/typography';
import { typographyCva } from '../../atoms/typography/cva';

type Tags = 'a' | 'button';
type ButtonType = 'button' | 'submit';

type Props = VariantProps<typeof buttonCva> & {
  label?: string;
  className?: string;
  asLink?: boolean;
  href?: string;
  type?: ButtonType;
  textVariant?: VariantProps<typeof typographyCva>['variant'];
  onClick?: () => void;
};

const Button = ({
  label,
  className,
  asLink,
  variant,
  size,
  full,
  disabled,
  glass,
  popup,
  textVariant,
  ...props
}: Props) => {
  const Tag: Tags = asLink ? 'a' : 'button';

  return (
    <Tag className={buttonCva({ variant, size, full, disabled, glass, popup, className })} {...props}>
      <Typography variant={textVariant || 'button'} textColor="controlled" bold center>
        {label}
      </Typography>
    </Tag>
  );
};

export default Button;
