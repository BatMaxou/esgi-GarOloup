'use client';

import { InputHTMLAttributes, useCallback, useMemo, useState } from 'react';
import cn from 'classnames';

import Typography from '@/components/ui/atoms/typography';
import Button from '@/components/ui/molecules/button';

type Props = Omit<InputHTMLAttributes<HTMLInputElement>, 'onChange'> & {
  unit?: string;
  defaultValue?: number;
  onChange?: (value: number) => void;
  onIncrement?: (value: number) => void;
  onDecrement?: (value: number) => void;
  className?: string;
};

const NumberInput = ({
  unit,
  defaultValue = 1,
  onChange,
  onIncrement,
  onDecrement,
  className,
  min,
  max,
  ...props
}: Props) => {
  const [value, setValue] = useState<number>(defaultValue);
  const [parsedMin, parsedMax] = useMemo(
    () => [min, max].map((value) => (value === undefined || typeof value === 'number' ? value : parseInt(value))),
    [min, max]
  );

  const handleChange = useCallback(
    (newValue: number) => {
      if ((parsedMin !== undefined && newValue < parsedMin) || (parsedMax !== undefined && newValue > parsedMax)) {
        return;
      }

      setValue(newValue);
      onChange?.(newValue);
    },
    [onChange, parsedMin, parsedMax]
  );

  const handleIncrement = useCallback(() => {
    handleChange(value + 1);
    onIncrement?.(value + 1);
  }, [handleChange, onIncrement, value]);

  const handleDecrement = useCallback(() => {
    handleChange(value - 1);
    onDecrement?.(value - 1);
  }, [handleChange, onDecrement, value]);

  return (
    <div className={cn('flex flex-row items-center justify-between gap-2', className)}>
      <input type="number" value={value} className="hidden" readOnly {...props} />

      <Button
        type="button"
        variant="neutral"
        size="xs"
        className="aspect-square"
        glass
        onClick={handleDecrement}
        leftIcon="minus"
      />
      <div className="flex items-baseline gap-1">
        <Typography variant="controlled" textColor="light" bold>
          {value}
        </Typography>
        {unit && (
          <Typography variant="controlled" textColor="neutral-300">
            {unit}
          </Typography>
        )}
      </div>
      <Button type="button" variant="neutral" size="xs" className="" glass onClick={handleIncrement} rightIcon="plus" />
    </div>
  );
};

export default NumberInput;
