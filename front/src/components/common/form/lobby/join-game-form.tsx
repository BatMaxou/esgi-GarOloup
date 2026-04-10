'use client';

import { useState } from 'react';
import { useFormik } from 'formik';
import { useTranslations } from 'next-intl';
import cn from 'classnames';

import Button from '@/components/ui/molecules/button';
import Divider from '@/components/ui/atoms/divider';
import OTPInput from '@/components/ui/molecules/otp-input';
import { useApiClient } from '@/contexts/api-context';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { toast } from 'react-toastify';
import { paths } from '@/utils/paths';
import { useRouter } from '@/i18n/navigation';

const CODE_LENGTH = 8;

type JoinGameFormValues = {
  code: string;
};

type Props = {
  className?: string;
};

const JoinGameForm = ({ className }: Props) => {
  const t = useTranslations('components.common.form.lobby.joinGame');
  const { apiClient } = useApiClient();
  const router = useRouter();
  const [code, setCode] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const { handleSubmit, handleChange } = useFormik({
    initialValues: {
      code: '',
    },
    onSubmit: async (values: JoinGameFormValues) => {
      setIsLoading(true);
      const response = await apiClient.game.join({ joinCode: values.code });
      if (!(response instanceof ApiClientError)) {
        toast.success(t('joinGameSuccess'));
        setIsLoading(false);
        router.push(paths.game);
        return;
      } else {
        if (response.code === 409) {
          toast.success(t('alreadyPlayingJoinCurrentGame'));
          setIsLoading(false);
          router.push(paths.game);
          return;
        }
        toast.error(t('joinGameError'));
      }
      setIsLoading(false);
    },
  });

  const isComplete = (code?.length ?? 0) === CODE_LENGTH;

  return (
    <form onSubmit={handleSubmit} className={cn('flex flex-col gap-4 p-8', className)}>
      <OTPInput
        length={CODE_LENGTH}
        label={t('codeLabel')}
        name="code"
        inputMode="text"
        value={code}
        onChange={(code) => {
          handleChange({ target: { name: 'code', value: code } });
          setCode(code);
        }}
        aria-label={t('codeAriaLabel')}
      />
      <Divider variant="primary" />
      <Button
        type="submit"
        variant="accent"
        label={t('submit')}
        full
        onClick={handleSubmit}
        disabled={!isComplete || isLoading}
      />
    </form>
  );
};

export default JoinGameForm;
