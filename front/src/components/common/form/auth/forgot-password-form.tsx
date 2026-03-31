'use client';

import { useFormik } from 'formik';
import cn from 'classnames';
import { useTranslations } from 'next-intl';
import { toast } from 'react-toastify';

import Button from '@/components/ui/molecules/button';
import TextInput from '@/components/ui/molecules/text-input';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { useApiClient } from '@/contexts/api-context';
import { useRouter } from '@/i18n/navigation';
import { paths } from '@/utils/paths';

type Props = {
  className?: string;
};

type ForgotPasswordFormValues = {
  email: string;
};

const ForgotPasswordForm = ({ className }: Props) => {
  const { apiClient } = useApiClient();
  const router = useRouter();
  const t = useTranslations('components.common.form.auth.forgotPassword');

  const { handleSubmit, handleChange } = useFormik({
    initialValues: {
      email: '',
    },
    onSubmit: async (values: ForgotPasswordFormValues) => {
      const response = await apiClient.user.forgotPassword(values.email);
      if (response instanceof ApiClientError) {
        toast.error(t('emailError'));
        return;
      }
      toast.success(t('emailSent'));
      router.push(paths.login);
    },
  });

  return (
    <form onSubmit={handleSubmit} className={cn('flex flex-col gap-4', className)}>
      <TextInput
        label={t('emailLabel')}
        type="email"
        name="email"
        placeholder={t('emailPlaceholder')}
        onChange={handleChange}
      />
      <Button variant="accent" label={t('submit')} type="submit" full />
    </form>
  );
};

export default ForgotPasswordForm;
