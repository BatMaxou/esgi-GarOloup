'use client';

import { ReactNode } from 'react';
import type { VariantProps } from 'class-variance-authority';

import { buttonCva } from './cva';
import Typography from '@/components/ui/atoms/typography';
import { typographyCva } from '@/components/ui/atoms/typography/cva';
import { Link } from '@/i18n/navigation';
import { pathnames } from '@/i18n/pathnames';

type ButtonType = 'button' | 'submit';

type Props = VariantProps<typeof buttonCva> & {
  label?: string;
  className?: string;
  asLink?: boolean;
  href?: keyof typeof pathnames;
  type?: ButtonType;
  textVariant?: VariantProps<typeof typographyCva>['variant'];
  onClick?: () => void;
};

const Wrapper = ({
  className,
  asLink,
  href,
  variant,
  size,
  full,
  disabled,
  glass,
  popup,
  children,
  ...props
}: Omit<Props, 'label' | 'textVariant'> & { children: ReactNode }) => {
  const classes = buttonCva({ variant, size, full, disabled, glass, popup, className });

  if (asLink && href) {
    return (
      <Link href={href} className={classes} {...props}>
        {children}
      </Link>
    );
  }

  return (
    <button className={classes} {...props}>
      {children}
    </button>
  );
};

const Button = ({ label, textVariant, ...props }: Props) => {
  return (
    <Wrapper {...props}>
      <Typography variant={textVariant || 'button'} textColor="controlled" bold center>
        {label}
      </Typography>
    </Wrapper>
  );
};

export default Button;
