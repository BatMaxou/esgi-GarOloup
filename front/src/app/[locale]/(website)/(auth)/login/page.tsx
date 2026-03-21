import LoginClient from '@/components/pages/auth/login-client';
import { redirect } from '@/i18n/navigation';
import { paths } from '@/utils/paths';
import { getSession } from '@/utils/server/clients';
import { getLocale } from 'next-intl/server';

const LoginPage = async () => {
  const session = await getSession();
  const locale = await getLocale();

  if (session?.user?.token) {
    redirect({ href: paths.game, locale });
  }

  return <LoginClient />;
};

export default LoginPage;
