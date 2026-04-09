'use client';

import {
  useCallback,
  useEffect,
  useMemo,
  useRef,
  type ChangeEvent,
  type ClipboardEvent,
  type KeyboardEvent,
} from 'react';
import type { VariantProps } from 'class-variance-authority';
import cn from 'classnames';

import { otpDigitCva } from './cva';
import Typography from '@/components/ui/atoms/typography';
import { uuidv4 } from 'zod';

type Props = VariantProps<typeof otpDigitCva> & {
  length?: number;
  value: string | null;
  defaultValue?: string;
  onChange?: (otp: string) => void;
  onComplete?: (otp: string) => void;
  label?: string;
  disabled?: boolean;
  error?: boolean;
  success?: boolean;
  className?: string;
  groupClassName?: string;
  name?: string;
  id?: string;
  inputMode?: 'numeric' | 'text';
  autoFocus?: boolean;
};

const OTPInput = ({
  length = 6,
  value = null,
  onChange,
  onComplete,
  label,
  disabled = false,
  error = false,
  success = false,
  className,
  groupClassName,
  name,
  id: idProp,
  inputMode = 'numeric',
  autoFocus,
  sizing,
}: Props) => {
  const uuid = uuidv4();
  const groupId = idProp ?? `otp-${uuid}`;
  const inputsRef = useRef<(HTMLInputElement | null)[]>([]);
  const setOtp = useCallback(
    (raw: string) => {
      const cleaned = inputMode === 'numeric' ? raw.replace(/\D/g, '') : raw.replace(/\s/g, '');
      const next = cleaned.slice(0, length);
      onChange?.(next);
      if (next.length === length) {
        onComplete?.(next);
      }
    },
    [inputMode, length, onChange, onComplete]
  );

  const digits = useMemo(
    () => Array.from({ length }, (_, i) => value?.[i] ?? ''),
    [value, length]
  );

  const status = disabled ? 'disabled' : error ? 'error' : success ? 'success' : 'default';

  const focusInput = (index: number) => {
    const input = inputsRef.current[index];
    if (input) {
      input.focus();
      input.select();
    }
  };

  /** Saisie sur la case `index` : met à jour la chaîne OTP complète et avance le focus si besoin. */
  const handleChange = (index: number, e: ChangeEvent<HTMLInputElement>) => {
    if (disabled) return;

    const current = value ?? '';
    const targetValue = e.target.value;

    // Champ vidé (effacement) : on retire le caractère à cette position sans toucher au reste.
    if (targetValue === '') {
      const next = `${current.slice(0, index)}${current.slice(index + 1)}`;
      setOtp(next);
      return;
    }

    // vérification du caractère entré en fonction du mode de saisie.
    const char = targetValue.slice(-1);
    if (inputMode === 'numeric' && !/^\d$/.test(char)) return;
    if (inputMode === 'text' && char.length !== 1) return;

    // Reconstruction de la chaine complète en remplaçant le caractère à la position `index` par `char`
    const before = current.slice(0, index);
    const after = current.slice(index + 1);
    const next = `${before}${char}${after}`.slice(0, length);
    setOtp(next);

    // Focus sur le prochain champ .
    if (char && index < length - 1) {
      focusInput(index + 1);
    }
  };

  const handleKeyDown = (index: number, e: KeyboardEvent<HTMLInputElement>) => {
    if (disabled) return;
    if (e.key === 'Backspace' && !value?.[index] && index > 0) {
      e.preventDefault();
      focusInput(index - 1);
    }
    if (e.key === 'ArrowLeft' && index > 0) {
      e.preventDefault();
      focusInput(index - 1);
    }
    if (e.key === 'ArrowRight' && index < length - 1) {
      e.preventDefault();
      focusInput(index + 1);
    }
  };

  const handlePaste = (e: ClipboardEvent<HTMLInputElement>) => {
    if (disabled) return;
    e.preventDefault();
    const text = e.clipboardData.getData('text').replace(/\s/g, '');
    const cleaned = inputMode === 'numeric' ? text.replace(/\D/g, '') : text;
    const next = cleaned.slice(0, length);
    setOtp(next);
    const idx = Math.min(Math.max(next.length - 1, 0), length - 1);
    requestAnimationFrame(() => focusInput(idx));
  };

  useEffect(() => {
    if (autoFocus && !disabled) {
      focusInput(0);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps -- autoFocus seulement au montage
  }, []);

  return (
    <div className={cn('flex flex-col gap-2', className)}>
      {label && (
        <label htmlFor={`${groupId}-0`} className="block cursor-text">
          <Typography variant="body-sm" textColor="neutral-500" bold uppercase tag="span">
            {label}
          </Typography>
        </label>
      )}
      <div
        className={cn('flex flex-row items-center gap-2 sm:gap-3', groupClassName)}
        role="group"
      >
        {name && <input type="hidden" name={name} value={value ?? ''} readOnly aria-hidden tabIndex={-1} />}
        {digits.map((digit, index) => (
          <input
            key={`${groupId}-${index}`}
            ref={(el) => {
              inputsRef.current[index] = el;
            }}
            id={`${groupId}-${index}`}
            type="text"
            inputMode={inputMode === 'numeric' ? 'numeric' : 'text'}
            autoComplete="one-time-code"
            maxLength={1}
            value={digit}
            disabled={disabled}
            aria-invalid={error || undefined}
            className={otpDigitCva({ sizing, status })}
            onChange={(e) => handleChange(index, e)}
            onKeyDown={(e) => handleKeyDown(index, e)}
            onPaste={handlePaste}
          />
        ))}
      </div>
    </div>
  );
};

export default OTPInput;
