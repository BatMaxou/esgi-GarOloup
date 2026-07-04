'use client';

import { type ReactNode, useCallback, useId, useState } from 'react';
import cn from 'classnames';

import Typography from '../../atoms/typography';
import DropdownChevron from './chevron';

type Props = {
  trigger: ReactNode;
  children: ReactNode;
  className?: string;
  open?: boolean;
  onOpenChange?: (open: boolean) => void;
  defaultOpen?: boolean;
};

const Dropdown = ({ trigger, children, className, open: controlledOpen, onOpenChange, defaultOpen = false }: Props) => {
  const panelId = useId();
  const [internalOpen, setInternalOpen] = useState(defaultOpen);
  const controlled = controlledOpen !== undefined;
  const open = controlled ? controlledOpen : internalOpen;

  const setOpen = useCallback(
    (next: boolean) => {
      if (!controlled) setInternalOpen(next);
      onOpenChange?.(next);
    },
    [controlled, onOpenChange]
  );

  const toggle = useCallback(() => setOpen(!open), [open, setOpen]);

  return (
    <div
      className={cn(
        'overflow-hidden rounded-xs border border-primary/12 bg-[rgba(26,28,46,0.95)] transition-colors duration-200',
        open && 'border-primary/30',
        className
      )}
    >
      <button
        type="button"
        className="flex w-full cursor-pointer select-none items-center justify-between gap-3 px-5 py-4 text-left text-[0.9rem] font-semibold text-neutral-200 transition-colors hover:text-primary-pastel"
        aria-expanded={open}
        aria-controls={panelId}
        onClick={toggle}
      >
        <Typography tag="span" className="min-w-0 flex-1">
          {trigger}
        </Typography>
        <DropdownChevron open={open} />
      </button>
      <div
        className={cn(
          'grid transition-[grid-template-rows] duration-300 ease-out',
          open ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'
        )}
      >
        <div id={panelId} className="min-h-0 overflow-hidden">
          <Typography tag="p" className="px-5 pb-4 text-[0.83rem] leading-[1.7] text-neutral-400">
            {children}
          </Typography>
        </div>
      </div>
    </div>
  );
};

export default Dropdown;
