import RegisterClient from '@/components/pages/auth/register-client';
import { redirect } from '@/i18n/navigation';
import { paths } from '@/utils/paths';
import { getSession } from '@/utils/server/clients';
import { getLocale } from 'next-intl/server';

const RegisterPage = async () => {
  const session = await getSession();
  const locale = await getLocale();

  if (session?.user?.token) {
    redirect({ href: paths.game, locale });
  }

  return <RegisterClient />;
};

export default RegisterPage;
