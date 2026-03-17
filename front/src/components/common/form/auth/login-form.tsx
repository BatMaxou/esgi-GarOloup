'use client';

import { useFormik } from 'formik';
import cn from 'classnames';

import Button from '@/components/ui/molecules/button';
import TextInput from '@/components/ui/molecules/text-input';
import { useAuth } from '@/contexts/auth-context';

type Props = {
  className?: string;
};

type LoginFormValues = {
  email: string;
  password: string;
};

const LoginForm = ({ className }: Props) => {
  const { login } = useAuth();

  const { handleSubmit, handleChange } = useFormik({
    initialValues: {
      email: '',
      password: '',
    },
    onSubmit: (values: LoginFormValues) => {
      login(values.email, values.password);
    },
  });

  return (
    <form onSubmit={handleSubmit} className={cn('flex flex-col gap-4', className)}>
      <TextInput label="email" type="email" name="email" placeholder="garoloup@example.com" onChange={handleChange} />
      <TextInput label="mot de passe" type="password" name="password" onChange={handleChange} />
      <Button
        asLink
        variant="text"
        label="Mot de passe oublié ?"
        textVariant="body-xs"
        className="text-neutral-500 self-end"
        href="#"
      />
      <Button variant="accent" label="Se connecter" type="submit" full />
    </form>
  );
};

export default LoginForm;
