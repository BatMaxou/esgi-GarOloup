import { ReactNode } from 'react';
import { getLocale } from 'next-intl/server';
import { redirect } from '@/i18n/navigation';

import { getSession } from '@/utils/server/clients';
import { paths } from '@/utils/paths';

type Props = {
  children: ReactNode;
};

const LoggedLayout = async ({ children }: Props) => {
  const session = await getSession();
  const locale = await getLocale();

  if (!session?.user?.token) {
    redirect({ href: paths.home, locale });
  }

  return <>{children}</>;
};

export default LoggedLayout;
