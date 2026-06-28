'use client';

import { useEffect, useRef, useState } from 'react';

type UseTimerParams = {
  stepEndAt: string;
  onTimeOut?: () => void;
};

const getRemainingSeconds = (stepEndAt: string) => {
  const diffInMs = new Date(stepEndAt).getTime() - Date.now();
  return Math.max(0, Math.ceil(diffInMs / 1000));
};

export const useTimer = ({ stepEndAt, onTimeOut }: UseTimerParams) => {
  const [remainingSeconds, setRemainingSeconds] = useState(() => getRemainingSeconds(stepEndAt));
  const [initialSeconds, setInitialSeconds] = useState(remainingSeconds);
  const [trackedStepEndAt, setTrackedStepEndAt] = useState(stepEndAt);

  const onTimeOutRef = useRef(onTimeOut);
  const hasTimedOutRef = useRef(false);

  useEffect(() => {
    onTimeOutRef.current = onTimeOut;
  }, [onTimeOut]);

  if (stepEndAt !== trackedStepEndAt) {
    const nextRemaining = getRemainingSeconds(stepEndAt);
    setTrackedStepEndAt(stepEndAt);
    setRemainingSeconds(nextRemaining);
    setInitialSeconds(nextRemaining);
  }

  useEffect(() => {
    hasTimedOutRef.current = false;

    const intervalId = setInterval(() => {
      setRemainingSeconds(getRemainingSeconds(stepEndAt));
    }, 1000);

    return () => clearInterval(intervalId);
  }, [stepEndAt]);

  useEffect(() => {
    if (remainingSeconds === 0 && !hasTimedOutRef.current) {
      hasTimedOutRef.current = true;
      onTimeOutRef.current?.();
    }
  }, [remainingSeconds]);

  return { remainingSeconds, initialSeconds };
};
