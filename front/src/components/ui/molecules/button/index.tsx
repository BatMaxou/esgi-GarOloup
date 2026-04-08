'use client';

import { ReactNode } from 'react';
import type { VariantProps } from 'class-variance-authority';
import cn from 'classnames';

import { buttonCva, buttonIconCva } from './cva';
import Typography from '@/components/ui/atoms/typography';
import { typographyCva } from '@/components/ui/atoms/typography/cva';
import { Link } from '@/i18n/navigation';
import { pathnames } from '@/i18n/pathnames';
import { IconName } from '@/components/ui/atoms/icon/config';
import Icon from '@/components/ui/atoms/icon';

type ButtonType = 'button' | 'submit';

type Props = VariantProps<typeof buttonCva> & {
  label?: string;
  className?: string;
  asLink?: boolean;
  href?: keyof typeof pathnames;
  type?: ButtonType;
  textVariant?: VariantProps<typeof typographyCva>['variant'];
  leftIcon?: IconName;
  rightIcon?: IconName;
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
  onClick,
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
    <button className={classes} onClick={onClick ? onClick : undefined} {...props}>
      {children}
    </button>
  );
};

const Button = ({ label, textVariant, leftIcon, rightIcon, className, ...props }: Props) => {
  return (
    <Wrapper className={cn('flex', className)} {...props}>
      {leftIcon && (
        <Typography variant={textVariant || 'button'} textColor="controlled">
          <Icon name={leftIcon} className={buttonIconCva({ size: props.size })} />
        </Typography>
      )}
      {label && (
        <Typography
          variant={textVariant || props.size === 'xs' ? 'body-xs' : 'button'}
          textColor="controlled"
          bold
          center
        >
          {label}
        </Typography>
      )}
      {rightIcon && (
        <Typography variant={textVariant || 'button'} textColor="controlled">
          <Icon name={rightIcon} className={buttonIconCva({ size: props.size })} />
        </Typography>
      )}
    </Wrapper>
  );
};

export default Button;
