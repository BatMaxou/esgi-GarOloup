'use client';

import { useFormik } from 'formik';
import cn from 'classnames';
import { useTranslations } from 'next-intl';
import { toast } from 'react-toastify';
import { version as uuidVersion } from 'uuid';
import { validate as uuidValidate } from 'uuid';

import Button from '@/components/ui/molecules/button';
import TextInput from '@/components/ui/molecules/text-input';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useApiClient } from '@/contexts/api-context';
import { useRouter } from '@/i18n/navigation';
import { paths } from '@/utils/paths';
import Typography from '@/components/ui/atoms/typography';
import { useSearchParams } from 'next/navigation';
import { useEffect } from 'react';

type Props = {
  className?: string;
};

type ResetPasswordFormValues = {
  password: string;
  passwordConfirmation: string;
};

const ResetPasswordForm = ({ className }: Props) => {
  const { apiClient } = useApiClient();
  const router = useRouter();
  const t = useTranslations('components.common.form.auth.resetPassword');

  const searchParams = useSearchParams();
  const token = searchParams.get('token');

  // Validator UUID v4
  // Permet de prévenir les injections depuis l'URL
  function uuidValidateV4(uuid: string) {
    return uuidValidate(uuid) && uuidVersion(uuid) === 4;
  }

  const invalidToken = !token || !uuidValidateV4(token);

  useEffect(() => {
    if (invalidToken) {
      toast.error(t('tokenError'));
      setTimeout(() => {
        router.push(paths.login);
      }, 2000);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const { handleSubmit, handleChange } = useFormik({
    initialValues: {
      password: '',
      passwordConfirmation: '',
    },
    onSubmit: async (values: ResetPasswordFormValues) => {
      if (invalidToken) {
        toast.error(t('tokenError'));
        return;
      }

      if (values.password !== values.passwordConfirmation) {
        toast.error(t('passwordMismatch'));
        return;
      }

      const response = await apiClient.user.resetPassword({ token: token, password: values.password });
      if (response instanceof ApiClientError) {
        toast.error(t('passwordUpdatedError'));
        return;
      }
      toast.success(t('passwordUpdated'));
      router.push(paths.login);
    },
  });

  return (
    <form onSubmit={handleSubmit} className={cn('flex flex-col gap-4', className)}>
      <TextInput label={t('passwordLabel')} type="password" name="password" onChange={handleChange} />
      <Typography variant="body-sm" textColor="neutral-500">
        {t('passwordRequirements')}
      </Typography>
      <TextInput
        label={t('passwordConfirmationLabel')}
        type="password"
        name="passwordConfirmation"
        onChange={handleChange}
      />
      <Button variant="accent" label={t('submit')} type="submit" full disabled={invalidToken} />
    </form>
  );
};

export default ResetPasswordForm;
