'use client';

import { cva } from 'class-variance-authority';
import { ReactNode } from 'react';

const glassPanelCva = cva(
  'flex justify-center bg-dark/40 border border-primary/20 backdrop-blur-sm rounded-sm shadow-(--shadow)'
);

type Props = {
  children: ReactNode;
  className?: string;
};

const GlassPanel = ({ children, className }: Props) => {
  return <div className={glassPanelCva({ className })}>{children}</div>;
};

export default GlassPanel;
