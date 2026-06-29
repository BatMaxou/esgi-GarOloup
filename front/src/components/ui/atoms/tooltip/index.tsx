'use client';

import type { ReactNode } from 'react';
import type { VariantProps } from 'class-variance-authority';
import cn from 'classnames';

import { tooltipArrowCva, tooltipCva } from './cva';

type Placement = NonNullable<VariantProps<typeof tooltipCva>['placement']>;

type Props = {
  children: ReactNode;
  content: ReactNode;
  placement?: Placement;
  open?: boolean;
  withArrow?: boolean;
  className?: string;
  contentClassName?: string;
};

const Tooltip = ({
  children,
  content,
  placement = 'top',
  open,
  withArrow = true,
  className,
  contentClassName,
}: Props) => {
  const visibility = open === undefined ? 'hover' : open ? 'visible' : 'hidden';

  return (
    <span className={cn('group/tooltip relative inline-flex w-fit', className)}>
      {children}
      <span role="tooltip" className={cn(tooltipCva({ placement, visibility }), contentClassName)}>
        {content}
        {withArrow && <span aria-hidden className={tooltipArrowCva({ placement })} />}
      </span>
    </span>
  );
};

export default Tooltip;
