'use client';

import { useCallback, useEffect, useState } from 'react';
import cn from 'classnames';
import { motion } from 'motion/react';

import { switchTrackCva, switchThumbCva, switchIconCva } from './cva';
import Icon from '@/components/ui/atoms/icon';
import { IconName } from '@/components/ui/atoms/icon/config';
import { VariantProps } from 'class-variance-authority';
import Typography from '@/components/ui/atoms/typography';
import { typographyCva } from '@/components/ui/atoms/typography/cva';

type SwitchSize = NonNullable<VariantProps<typeof switchTrackCva>['size']>;

const switchThumbInsetBySize: Record<SwitchSize, string> = {
  lg: 'top-1 left-1',
  md: 'top-0.75 left-0.75',
  sm: 'top-0.5 left-0.5',
};

/** Distance parcourue par le thumb dans la piste (track − padding − thumb). */
const switchThumbTravelBySize: Record<SwitchSize, string> = {
  lg: 'calc(4rem - 0.5rem - 1.5rem)',
  md: 'calc(3.5rem - 0.375rem - 1.25rem)',
  sm: 'calc(3rem - 0.25rem - 1rem)',
};

type Props = VariantProps<typeof switchTrackCva> & {
  defaultChecked?: boolean;
  checkedIcon?: IconName;
  checkedIconClassColor?: VariantProps<typeof typographyCva>['textColor'];
  uncheckedIcon?: IconName;
  uncheckedIconClassColor?: VariantProps<typeof typographyCva>['textColor'];
  className?: string;
  onChange?: (checked: boolean) => void;
};

const SwitchInput = ({
  variant = 'primary',
  size = 'md',
  checked: controlledChecked,
  defaultChecked = false,
  checkedIcon,
  checkedIconClassColor = 'controlled',
  uncheckedIcon,
  uncheckedIconClassColor = 'controlled',
  onChange,
  disabled = false,
  className,
  ...props
}: Props) => {
  const [checked, setChecked] = useState(defaultChecked);
  const resolvedSize = size ?? 'md';

  const handleToggle = useCallback(() => {
    if (disabled) {
      return;
    }

    const newChecked = !checked;
    setChecked(newChecked);
    onChange?.(newChecked);
  }, [checked, disabled, onChange]);

  useEffect(() => {
    if (undefined === controlledChecked) {
      return;
    }

    setChecked(!!controlledChecked); // eslint-disable-line react-hooks/set-state-in-effect
  }, [controlledChecked]);

  return (
    <div
      className={cn(switchTrackCva({ variant, checked, size: resolvedSize, disabled }), className)}
      onClick={handleToggle}
      role="switch"
      aria-checked={checked}
    >
      <input type="checkbox" checked={checked} readOnly className="hidden" disabled={!!disabled} {...props} />
      <motion.div
        className={cn(
          switchThumbCva({ variant, checked, size: resolvedSize }),
          'absolute',
          switchThumbInsetBySize[resolvedSize]
        )}
        initial={false}
        animate={{ x: checked ? switchThumbTravelBySize[resolvedSize] : 0 }}
        transition={{ type: 'spring', stiffness: 500, damping: 35 }}
      >
        {checked && checkedIcon && (
          <Typography textColor={checkedIconClassColor}>
            <Icon name={checkedIcon} className={cn('absolute inset-0 m-auto', switchIconCva({ size: resolvedSize }))} />
          </Typography>
        )}
        {!checked && uncheckedIcon && (
          <Typography textColor={uncheckedIconClassColor}>
            <Icon
              name={uncheckedIcon}
              className={cn('absolute inset-0 m-auto', switchIconCva({ size: resolvedSize }))}
            />
          </Typography>
        )}
      </motion.div>
    </div>
  );
};

export default SwitchInput;
