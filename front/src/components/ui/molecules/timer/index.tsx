'use client';

import { useId } from 'react';
import cn from 'classnames';

import Typography from '@/components/ui/atoms/typography';

import { useTimer } from './use-timer';

type Props = {
  stepEndAt: string;
  onTimeOut?: () => void;
  size?: number;
  strokeWidth?: number;
  className?: string;
};

const Timer = ({ stepEndAt, onTimeOut, size = 64, strokeWidth = 5, className }: Props) => {
  const gradientId = useId();
  const { remainingSeconds, initialSeconds } = useTimer({ stepEndAt, onTimeOut });

  const radius = (size - strokeWidth) / 2;
  const circumference = 2 * Math.PI * radius;

  const progress = initialSeconds > 0 ? remainingSeconds / initialSeconds : 0;
  const dashOffset = circumference * (1 - progress);

  return (
    <div
      className={cn('relative inline-flex items-center justify-center', className)}
      style={{ width: size, height: size }}
    >
      <svg className="-rotate-90" width={size} height={size} viewBox={`0 0 ${size} ${size}`}>
        <defs>
          <linearGradient id={gradientId} x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stopColor="var(--color-secondary)" />
            <stop offset="100%" stopColor="var(--color-primary)" />
          </linearGradient>
        </defs>

        <circle
          cx={size / 2}
          cy={size / 2}
          r={radius}
          fill="none"
          strokeWidth={strokeWidth}
          className="stroke-neutral-200/10"
        />

        <circle
          cx={size / 2}
          cy={size / 2}
          r={radius}
          fill="none"
          strokeWidth={strokeWidth}
          strokeLinecap="round"
          stroke={`url(#${gradientId})`}
          strokeDasharray={circumference}
          strokeDashoffset={dashOffset}
          className="transition-[stroke-dashoffset] duration-1000 ease-linear"
        />
      </svg>

      <Typography tag="span" variant="body" bold center className="absolute inset-0 flex items-center justify-center">
        {remainingSeconds}
      </Typography>
    </div>
  );
};

export default Timer;
