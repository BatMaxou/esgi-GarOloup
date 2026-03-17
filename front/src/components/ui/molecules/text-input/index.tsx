'use client';

import { InputHTMLAttributes } from 'react';
import type { VariantProps } from 'class-variance-authority';

import { textInputCva } from './cva';
import Typography from '@/components/ui/atoms/typography';

type Props = InputHTMLAttributes<HTMLInputElement> &
  VariantProps<typeof textInputCva> & {
    label?: string;
    className?: string;
  };

const TextInput = ({ label, sizing, fit, className, ...props }: Props) => {
  return (
    <div>
      {label && (
        <Typography variant="body-sm" textColor="neutral-500" bold uppercase>
          {label}
        </Typography>
      )}
      <Typography
        tag="input"
        variant="controlled"
        textColor="light"
        bold
        className={textInputCva({ sizing, fit, className })}
        {...props}
      />
    </div>
  );
};

export default TextInput;
