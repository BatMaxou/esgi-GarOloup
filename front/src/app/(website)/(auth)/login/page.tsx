import { redirect } from 'next/navigation';

import LoginClient from '@/components/pages/auth/login-client';
import { paths } from '@/utils/paths';
import { getSession } from '@/utils/server/clients';

const LoginPage = async () => {
  const session = await getSession();

  if (session?.user?.token) {
    redirect(paths.home);
  }

  return <LoginClient />;
};

export default LoginPage;
