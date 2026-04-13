'use client';

import type { ComponentProps, ReactNode } from 'react';
import type { VariantProps } from 'class-variance-authority';
import cn from 'classnames';

import { cardCva } from './cva';
import { Link } from '@/i18n/navigation';

type CardType = NonNullable<VariantProps<typeof cardCva>['type']>;
type CardOrientation = NonNullable<VariantProps<typeof cardCva>['orientation']>;
type RoleCardVariant = Exclude<NonNullable<VariantProps<typeof cardCva>['roleVariant']>, 'none'>;
type LinkHref = ComponentProps<typeof Link>['href'];

type BaseProps = {
  children: ReactNode;
  className?: string;
  orientation?: CardOrientation;
  liftOnHover?: boolean;
  fullfilled?: boolean;
};

type DefaultCardProps = BaseProps & {
  type?: 'default';
  variant?: 'none' | 'accent' | 'success' | 'error';
  fullfilled?: boolean;
  href?: LinkHref;
};

type RoleCardProps = BaseProps & {
  type: 'role';
  variant: RoleCardVariant;
  href?: LinkHref;
};

type Props = DefaultCardProps | RoleCardProps;

const Card = ({
  children,
  type = 'default',
  variant,
  className,
  orientation = 'vertical',
  liftOnHover = true,
  fullfilled = false,
  href,
}: Props) => {
  const isRole = type === 'role';
  const roleVariant = isRole ? (variant as RoleCardVariant) : 'none';
  const emphasis = !isRole
    ? variant === 'accent'
      ? 'accent'
      : variant === 'success'
        ? 'success'
        : variant === 'error'
          ? 'error'
          : 'base'
    : 'base';

  return href ? (
    <Link
      href={href}
      className={cn(
        cardCva({ orientation, liftOnHover, type, roleVariant, emphasis, fullfilled, className }),
        href ? 'cursor-pointer' : ''
      )}
    >
      {children}
    </Link>
  ) : (
    <div className={`${cardCva({ orientation, liftOnHover, type, roleVariant, emphasis, fullfilled, className })}`}>
      {children}
    </div>
  );
};

export default Card;
export type { CardType, CardOrientation, RoleCardVariant, Props as CardProps };
