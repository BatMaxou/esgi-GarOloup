'use client';

import { ReactNode } from 'react';
import type { VariantProps } from 'class-variance-authority';

import { typographyCva } from './cva';

type Tags = 'span' | 'div' | 'p' | 'h1' | 'h2' | 'h3' | 'a' | 'input';

type Props = VariantProps<typeof typographyCva> & {
  children?: ReactNode;
  className?: string;
  tag?: Tags;
};

const Typography = ({
  children,
  className,
  tag,
  variant,
  textColor,
  bold,
  center,
  underline,
  ellipsis,
  special,
  ...props
}: Props) => {
  const Tag: Tags = tag || 'span';

  return (
    <Tag
      className={typographyCva({
        variant,
        textColor,
        bold,
        center,
        underline,
        ellipsis,
        special,
        className,
      })}
      {...props}
    >
      {children}
    </Tag>
  );
};

export default Typography;
