'use client';

import { useCallback, useEffect, useState } from 'react';
import cn from 'classnames';
import { motion } from 'motion/react';

import Icon from '@/components/ui/atoms/icon';
import { IconName } from '@/components/ui/atoms/icon/config';

import { switchTrackCva, switchThumbCva, switchIconCva } from './cva';
import { VariantProps } from 'class-variance-authority';
import Typography from '../../atoms/typography';
import { typographyCva } from '../../atoms/typography/cva';

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
      className={cn(switchTrackCva({ variant, checked, size, disabled }), className)}
      onClick={handleToggle}
      role="switch"
      aria-checked={checked}
    >
      <input type="checkbox" checked={checked} readOnly className="hidden" disabled={!!disabled} {...props} />
      <motion.div layout className={switchThumbCva({ variant, checked, size })}>
        {checked && checkedIcon && (
          <Typography textColor={checkedIconClassColor}>
            <Icon name={checkedIcon} className={cn('absolute inset-0 m-auto', switchIconCva({ size }))} />
          </Typography>
        )}
        {!checked && uncheckedIcon && (
          <Typography textColor={uncheckedIconClassColor}>
            <Icon name={uncheckedIcon} className={cn('absolute inset-0 m-auto', switchIconCva({ size }))} />
          </Typography>
        )}
      </motion.div>
    </div>
  );
};

export default SwitchInput;
