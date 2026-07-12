'use client';

import cn from 'classnames';

import Typography from '@/components/ui/atoms/typography';
import Tag, { type TagVariantType } from '@/components/ui/molecules/tag';

type Option = {
  label: string;
  value: string;
};

type Props = {
  label?: string;
  name?: string;
  value: string[];
  options: Option[];
  onChange: (value: string[]) => void;
  tagVariant?: (value: string) => TagVariantType;
  className?: string;
};

const MultiSelectInput = ({ label, name, value, options, onChange, tagVariant, className }: Props) => {
  const toggleOption = (optionValue: string) => {
    if (value.includes(optionValue)) {
      onChange(value.filter((selected) => selected !== optionValue));
      return;
    }

    onChange([...value, optionValue]);
  };

  return (
    <div className={cn('flex flex-col gap-2', className)}>
      {label && (
        <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span" className="block">
          {label}
        </Typography>
      )}
      <div
        role="group"
        aria-label={label}
        className="flex flex-wrap gap-2 rounded-xs border border-primary/12 bg-[rgba(26,28,46,0.9)] p-3"
      >
        {options.map((option) => {
          const isSelected = value.includes(option.value);

          return (
            <button
              key={option.value}
              type="button"
              name={name ? `${name}-${option.value}` : undefined}
              className="cursor-pointer border-0 bg-transparent p-0"
              aria-pressed={isSelected}
              onClick={() => toggleOption(option.value)}
            >
              <Tag
                label={option.label}
                size="lg"
                variant={tagVariant ? tagVariant(option.value) : 'secondary'}
                active={isSelected}
                lowerCase
              />
            </button>
          );
        })}
      </div>
    </div>
  );
};

export default MultiSelectInput;
