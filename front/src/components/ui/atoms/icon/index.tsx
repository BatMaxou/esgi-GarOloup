'use client';

import { LucideProps } from 'lucide-react';

import { icons } from './config';

type Props = LucideProps & {
  name: keyof typeof icons;
  title?: string;
};

const Icon = ({ name, title = '', ...props }: Props) => {
  const IconComponent = icons[name];

  return <span title={title}>{IconComponent && <IconComponent {...props} />}</span>;
};

export default Icon;
