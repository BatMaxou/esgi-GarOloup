'use client';

import { useAuth } from '@/contexts/auth-context';
import { useFormik } from 'formik';

type LoginFormValues = {
  email: string;
  password: string;
};

const LoginClient = () => {
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
    <form onSubmit={handleSubmit}>
      <input
        className="border border-primary"
        type="email"
        name="email"
        onChange={handleChange}
      />
      <input
        className="border border-primary"
        type="password"
        name="password"
        onChange={handleChange}
      />
      <button className="bg-primary text-white px-4 py-2 rounded-full">
        test
      </button>
    </form>
  );
};

export default LoginClient;
