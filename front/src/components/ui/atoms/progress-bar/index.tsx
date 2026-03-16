'use client';

import { useEffect, useRef } from 'react';
import cn from 'classnames';

type Props = {
  value: number;
  total?: number;
  className?: string;
};

const ProgressBar = ({ value, total = 100, className }: Props) => {
  const progressBarCompletionElementRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (progressBarCompletionElementRef.current) {
      progressBarCompletionElementRef.current.style.setProperty(
        '--progress-bar-completion',
        `${(value / total) * 100}%`
      );
    }
  }, [value, total]);

  return (
    <div className={cn('w-full h-1 bg-neutral-200/10 rounded-md overflow-hidden', className)}>
      <div
        ref={progressBarCompletionElementRef}
        className="w-[var(--progress-bar-completion)] h-full bg-linear-(--primary-gradient) rounded-md"
      ></div>
    </div>
  );
};

export default ProgressBar;
