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
  hoverable?: boolean;
  liftOnHover?: boolean;
  fullfilled?: boolean;
  isCurrentPlayer?: boolean;
};

type DefaultCardProps = BaseProps & {
  type?: 'default';
  variant?: 'none' | 'accent' | 'success' | 'error' | 'gradient' | 'likeHeader' | 'player';
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
  variant = 'none',
  className,
  orientation = 'vertical',
  liftOnHover = true,
  hoverable = true,
  fullfilled = false,
  isCurrentPlayer = false,
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
          : variant === 'gradient'
            ? 'gradient'
            : variant === 'likeHeader'
              ? 'likeHeader'
              : variant === 'player'
                ? 'player'
                : 'base'
    : 'base';

  const playerHighlight = emphasis === 'player' ? (isCurrentPlayer ? 'current' : 'default') : 'default';

  return href ? (
    <Link
      href={href}
      className={cn(
        cardCva({
          orientation,
          liftOnHover: hoverable && liftOnHover,
          type,
          roleVariant,
          emphasis,
          playerHighlight,
          fullfilled,
          className,
        }),
        href ? 'cursor-pointer' : ''
      )}
    >
      {children}
    </Link>
  ) : (
    <div
      className={cn(
        cardCva({
          orientation,
          liftOnHover: hoverable && liftOnHover,
          type,
          roleVariant,
          emphasis,
          playerHighlight,
          fullfilled,
        }),
        className
      )}
    >
      {children}
    </div>
  );
};

export default Card;
export type { CardType, CardOrientation, RoleCardVariant, Props as CardProps };
