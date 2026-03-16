'use client';

import { InputHTMLAttributes } from 'react';
import type { VariantProps } from 'class-variance-authority';

import { textInputCva } from './cva';
import Typography from '@/components/ui/atoms/typography';

type Props = InputHTMLAttributes<HTMLInputElement> &
  VariantProps<typeof textInputCva> & {
    className?: string;
  };

const TextInput = ({ sizing, fit, className, ...props }: Props) => {
  return (
    <Typography
      tag="input"
      variant="controlled"
      textColor="light"
      bold
      className={textInputCva({ sizing, fit, className })}
      {...props}
    />
  );
};

export default TextInput;
