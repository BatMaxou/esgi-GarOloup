'use client';

import { useState } from 'react';

import Tag, { TagVariantType } from '@/components/ui/molecules/tag';
import { useTranslations } from 'next-intl';
import TextSkeleton from '../../atoms/skeleton';
import cn from 'classnames';

export type TagFilterProps<T extends string = string> = {
  labels: T[];
  allLabel?: string;
  className?: string;
  onSelectedChange?: (value: T | 'all') => void;
  selected?: T | 'all';
  defaultSelected?: T | 'all';
  traductionPath?: string;
  isLoading?: boolean;
  tagVariant?: (value: T) => TagVariantType;
};

function TagFilter<T extends string>({
  labels,
  allLabel = 'Tout',
  className,
  onSelectedChange,
  selected: selectedControlled,
  defaultSelected = 'all',
  traductionPath,
  isLoading,
  tagVariant,
}: TagFilterProps<T>) {
  const [selectedInternal, setSelectedInternal] = useState<T | 'all'>(defaultSelected);
  const isControlled = selectedControlled !== undefined;
  const selected = isControlled ? selectedControlled : selectedInternal;
  const t = useTranslations(traductionPath);
  const setSelected = (value: T | 'all') => {
    if (!isControlled) {
      setSelectedInternal(value);
    }
    onSelectedChange?.(value);
  };

  return (
    <div className={cn('flex flex-wrap items-center gap-2', className, isLoading ? 'w-full' : '')}>
      {isLoading ? (
        <>
          <TextSkeleton variant="body" className="max-w-12!" />
          <TextSkeleton variant="body" className="max-w-12!" />
          <TextSkeleton variant="body" className="max-w-12!" />
          <TextSkeleton variant="body" className="max-w-12!" />
        </>
      ) : (
        <>
          <button
            type="button"
            className="cursor-pointer border-0 bg-transparent p-0"
            onClick={() => setSelected('all')}
          >
            <Tag label={allLabel} size="lg" variant={'secondary'} active={selected === 'all'} lowerCase />
          </button>
          {labels.map((label) => {
            const active = selected === label;
            return (
              <button
                key={label}
                type="button"
                className="cursor-pointer border-0 bg-transparent p-0 rounded-xs"
                onClick={() => setSelected(label)}
              >
                <Tag
                  label={t(label as string)}
                  size="lg"
                  variant={tagVariant ? tagVariant(label as T) : 'secondary'}
                  active={active}
                  lowerCase
                />
              </button>
            );
          })}
        </>
      )}
    </div>
  );
}

export default TagFilter;
