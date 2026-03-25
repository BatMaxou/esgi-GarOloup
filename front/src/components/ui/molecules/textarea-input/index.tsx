'use client';

import { TextareaHTMLAttributes } from 'react';
import type { VariantProps } from 'class-variance-authority';
import Typography from '@/components/ui/atoms/typography';

import { textareaInputCva } from './cva';

type Props = TextareaHTMLAttributes<HTMLTextAreaElement> &
  VariantProps<typeof textareaInputCva> & {
    label?: string;
    className?: string;
  };

const TextareaInput = ({ label, sizing, fit, className, ...props }: Props) => {
  return (
    <div className="flex w-full flex-col gap-1">
      {label && (
        <label className="block font-bold">
          <Typography variant="body-sm" textColor="neutral-500" uppercase>
            {label}
          </Typography>
        </label>
      )}
      <textarea className={textareaInputCva({ sizing, fit, className })} {...props} />
    </div>
  );
};

export default TextareaInput;
