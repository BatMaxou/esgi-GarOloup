'use client';

import { useFormik } from 'formik';
import cn from 'classnames';
import { useTranslations } from 'next-intl';

import Button from '@/components/ui/molecules/button';
import TextInput from '@/components/ui/molecules/text-input';
import { toast } from 'react-toastify';
import { useApiClient } from '@/contexts/api-context';
import Typography from '@/components/ui/atoms/typography';

type Props = {
  className?: string;
};

type SigninFormValues = {
  username: string;
  email: string;
  password: string;
  passwordConfirmation?: string;
};

const SigninForm = ({ className }: Props) => {
  const t = useTranslations('components.common.form.auth.register');
  const { apiClient } = useApiClient();
  const { handleSubmit, handleChange } = useFormik({
    initialValues: {
      username: '',
      email: '',
      password: '',
      passwordConfirmation: '',
    },
    onSubmit: async (values: SigninFormValues) => {
      if (values.password !== values.passwordConfirmation) {
        toast.error(t('passwordMismatch'));
        return;
      }
      await apiClient.user.register({ email: values.email, username: values.username, password: values.password });
      return;
    },
  });

  return (
    <form onSubmit={handleSubmit} className={cn('flex flex-col gap-4', className)}>
      <TextInput
        label={t('usernameLabel')}
        type="text"
        name="username"
        placeholder={t('usernamePlaceholder')}
        onChange={handleChange}
      />
      <TextInput
        label={t('emailLabel')}
        type="email"
        name="email"
        placeholder={t('emailPlaceholder')}
        onChange={handleChange}
      />
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
      <Button variant="accent" label={t('submit')} type="submit" full />
    </form>
  );
};

export default SigninForm;
