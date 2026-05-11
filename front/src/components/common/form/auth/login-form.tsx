'use client';

import { useFormik } from 'formik';
import cn from 'classnames';
import { useTranslations } from 'next-intl';

import Button from '@/components/ui/molecules/button';
import TextInput from '@/components/ui/molecules/text-input';
import { useAuth } from '@/contexts/auth-context';

type Props = {
  className?: string;
  onSuccess?: () => void;
};

type LoginFormValues = {
  email: string;
  password: string;
};

const LoginForm = ({ className, onSuccess }: Props) => {
  const { login, isLoginLoading } = useAuth();
  const t = useTranslations('components.common.form.auth.login');

  const { handleSubmit, handleChange } = useFormik({
    initialValues: {
      email: '',
      password: '',
    },
    onSubmit: async (values: LoginFormValues) => {
      const response = await login(values.email, values.password);
      if (response) {
        onSuccess?.();
      }
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
      <TextInput label={t('passwordLabel')} type="password" name="password" onChange={handleChange} />
      <Button
        asLink
        variant="text"
        label={t('forgotPassword')}
        textVariant="body-xs"
        className="text-neutral-500 self-end"
        href="/forgot-password"
      />
      <Button variant="accent" label={t('submit')} loading={isLoginLoading} type="submit" full />
    </form>
  );
};

export default LoginForm;
