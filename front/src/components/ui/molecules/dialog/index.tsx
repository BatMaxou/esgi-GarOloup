'use client';

import { type ReactNode, useEffect, useId } from 'react';
import type { VariantProps } from 'class-variance-authority';

import { dialogBackdropCva, dialogPanelCva } from './cva';
import Typography from '@/components/ui/atoms/typography';
import Button from '../button';

type PanelSize = NonNullable<VariantProps<typeof dialogPanelCva>['size']>;

type Props = {
  open: boolean;
  setOpen: (open: boolean) => void;
  children: ReactNode;
  title?: ReactNode;
  description?: ReactNode;
  size?: PanelSize;
  isClosable?: boolean;
  className?: string;
};

const Dialog = ({ open, setOpen, title, description, size, isClosable = true, className, children }: Props) => {
  const titleId = useId();
  const descriptionId = useId();

  useEffect(() => {
    if (!open) return;
    const onKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') setOpen(false);
    };
    document.addEventListener('keydown', onKeyDown);
    const prevOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    return () => {
      document.removeEventListener('keydown', onKeyDown);
      document.body.style.overflow = prevOverflow;
    };
  }, [open, setOpen]);

  if (!open) return null;

  return (
    <div className="fixed inset-0 z-50" role="presentation">
      <button
        type="button"
        className={`absolute inset-0 ${dialogBackdropCva()}`}
        aria-label="Fermer la fenêtre"
        onClick={() => setOpen(false)}
      />
      <div className="pointer-events-none relative z-10 flex min-h-full items-center justify-center p-4">
        <div
          role="dialog"
          aria-modal="true"
          aria-labelledby={title ? titleId : undefined}
          aria-describedby={description ? descriptionId : undefined}
          className={`pointer-events-auto ${dialogPanelCva({ size, className })}`}
        >
          <div className="relative mb-2 flex items-start justify-between gap-4 border-b border-primary/10 pb-4">
            {title != null && title !== '' ? (
              <Typography tag="h2" variant="heading-3" bold className="min-w-0 flex-1 pr-10 text-light">
                <span id={titleId}>{title}</span>
              </Typography>
            ) : null}
            {isClosable && (
              <Button
                variant="text"
                label="Fermer"
                type="button"
                onClick={() => setOpen(false)}
                className="absolute right-0 top-0 shrink-0"
              />
            )}
          </div>
          {description != null && description !== '' && (
            <p id={descriptionId} className="mb-4 text-[0.875rem] leading-5.5 text-neutral-500">
              {description}
            </p>
          )}
          {children}
        </div>
      </div>
    </div>
  );
};

export default Dialog;
export type { Props as DialogProps };
