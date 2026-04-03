'use client';

import type { ReactNode } from 'react';
import type { VariantProps } from 'class-variance-authority';

import { cardCva } from './cva';
import { getImagePath } from '@/utils/getImagePath';

type CardType = NonNullable<VariantProps<typeof cardCva>['type']>;
type CardOrientation = NonNullable<VariantProps<typeof cardCva>['orientation']>;
type RoleCardVariant = Exclude<NonNullable<VariantProps<typeof cardCva>['roleVariant']>, 'none'>;

type BaseProps = {
  children: ReactNode;
  className?: string;
  orientation?: CardOrientation;
  liftOnHover?: boolean;
  fullfilled?: boolean;
  imagePath?: string;
};

type DefaultCardProps = BaseProps & {
  type?: 'default';
  variant?: 'none' | 'accent' | 'success' | 'error';
  fullfilled?: boolean;
};

type RoleCardProps = BaseProps & {
  type: 'role';
  variant: RoleCardVariant;
};

type Props = DefaultCardProps | RoleCardProps;

const Card = (props: Props) => {
  const { children, className, orientation = 'vertical', liftOnHover = true, fullfilled = false, imagePath } = props;
  const isRole = props.type === 'role';
  const type = isRole ? 'role' : 'default';
  const roleVariant = isRole ? props.variant : 'none';
  const emphasis = !isRole
    ? props.variant === 'accent'
      ? 'accent'
      : props.variant === 'success'
        ? 'success'
        : props.variant === 'error'
          ? 'error'
          : 'base'
    : 'base';

  return (
    <div
      className={` ${cardCva({ orientation, liftOnHover, type, roleVariant, emphasis, fullfilled, className })} ${imagePath ? `bg-[url(${getImagePath(imagePath)})] bg-cover bg-center` : ''}`}
    >
      {children}
    </div>
  );
};

export default Card;
export type { CardType, CardOrientation, RoleCardVariant, Props as CardProps };
