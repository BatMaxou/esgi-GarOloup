'use client';

import { useFormik } from 'formik';
import cn from 'classnames';
import { useTranslations } from 'next-intl';

import Button from '@/components/ui/molecules/button';
import TextInput from '@/components/ui/molecules/text-input';
import Typography from '@/components/ui/atoms/typography';
import Divider from '@/components/ui/atoms/divider';
import { useAuth } from '@/contexts/auth-context';

type Props = {
  className?: string;
  onSuccess: () => void;
};

type TempUserRegistrationFormValues = {
  username: string;
};

const TempUserRegistrationForm = ({ className, onSuccess }: Props) => {
  const t = useTranslations('components.common.form.auth.register');
  const { getTempUser } = useAuth();
  const { values, handleSubmit, handleChange } = useFormik({
    initialValues: {
      username: '',
    },
    onSubmit: async (values: TempUserRegistrationFormValues) => {
      const response = await getTempUser(values.username);
      if (response) onSuccess();
    },
  });

  return (
    <form onSubmit={handleSubmit} className={cn('flex flex-col gap-12 w-full', className)}>
      <div className="flex flex-col gap-2">
        <TextInput
          label={t('usernameLabel')}
          type="text"
          name="username"
          placeholder={t('usernamePlaceholder')}
          onChange={handleChange}
          className="w-full"
        />
        <Typography variant="body-sm" textColor="neutral-600">
          {t('usernameRequirements')}
        </Typography>
      </div>
      <Divider variant="neutral" className="w-full" />
      <Button variant="gradient" disabled={values.username.length === 0} label={t('submit')} type="submit" full />
    </form>
  );
};

export default TempUserRegistrationForm;
