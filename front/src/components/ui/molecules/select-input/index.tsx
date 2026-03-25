'use client';

import { SelectHTMLAttributes, useId } from 'react';
import type { VariantProps } from 'class-variance-authority';
import cn from 'classnames';

import DropdownChevron from '@/components/ui/molecules/dropdown/chevron';
import Typography from '@/components/ui/atoms/typography';

import { selectInputCva } from './cva';

type Props = SelectHTMLAttributes<HTMLSelectElement> &
  VariantProps<typeof selectInputCva> & {
    label?: string;
    className?: string;
    options: { label: string; value: string | number }[];
  };

const SelectInput = ({ label, sizing, fit, className, name, options, ...props }: Props) => {
  const selectId = useId();

  return (
    <div className={cn('flex flex-col gap-1', fit ? 'w-fit' : 'w-full')}>
      {label && (
        <label htmlFor={selectId} className="block">
          <Typography variant="body-sm" textColor="neutral-500" bold uppercase>
            {label}
          </Typography>
        </label>
      )}
      <div className="relative">
        <select id={selectId} name={name} className={selectInputCva({ sizing, fit, className })} {...props}>
          {options.map((option) => (
            <option key={String(option.value)} value={option.value}>
              {option.label}
            </option>
          ))}
        </select>
        <span
          className={cn(
            'pointer-events-none absolute inset-y-0 right-0 flex items-center',
            sizing === 'lg' && 'pr-4',
            sizing === 'sm' && 'pr-2',
            (sizing === 'md' || sizing == null) && 'pr-3'
          )}
        >
          <DropdownChevron />
        </span>
      </div>
    </div>
  );
};

export default SelectInput;
