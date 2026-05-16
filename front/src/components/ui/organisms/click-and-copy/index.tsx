'use client';

import { type ReactNode, useCallback, useEffect, useRef, useState } from 'react';
import cn from 'classnames';
import { motion } from 'motion/react';

import Icon from '../../atoms/icon';

const COPY_RESET_TIME = 2000;

const ClickAndCopy = ({
  children,
  valueToCopy,
  iconClassName,
  iconPosition = 'right',
}: {
  children: ReactNode;
  valueToCopy: string | number;
  iconClassName?: string;
  iconPosition?: 'left' | 'right';
}) => {
  const [copied, setCopied] = useState(false);
  const resetCopiedRef = useRef<ReturnType<typeof setTimeout> | null>(null);

  const handleClick = useCallback(() => {
    void navigator.clipboard.writeText(String(valueToCopy));
    setCopied(true);
    if (resetCopiedRef.current) clearTimeout(resetCopiedRef.current);
    resetCopiedRef.current = setTimeout(() => {
      setCopied(false);
      resetCopiedRef.current = null;
    }, COPY_RESET_TIME);
  }, [valueToCopy]);

  useEffect(
    () => () => {
      if (resetCopiedRef.current) clearTimeout(resetCopiedRef.current);
    },
    []
  );

  return (
    <div
      role="button"
      tabIndex={0}
      className="flex cursor-pointer items-center gap-2"
      onClick={handleClick}
      onKeyDown={(e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          handleClick();
        }
      }}
    >
      {iconPosition === 'left' && (
        <motion.div
          layout
          className={`transition-[padding] duration-300`}
          whileHover={{ scale: 1.1 }}
          whileTap={{ scale: 0.9 }}
          animate={{ opacity: 1 }}
        >
          <Icon name={copied ? 'check' : 'copy'} className={cn('h-4 w-4', iconClassName)} />
        </motion.div>
      )}
      {children}
      {iconPosition === 'right' && (
        <motion.div
          layout
          className={`transition-[padding] duration-300`}
          whileHover={{ scale: 1.1 }}
          whileTap={{ scale: 0.9 }}
          animate={{ opacity: 1 }}
        >
          <Icon name={copied ? 'check' : 'copy'} className={cn('h-4 w-4', iconClassName)} />
        </motion.div>
      )}
    </div>
  );
};

export default ClickAndCopy;
