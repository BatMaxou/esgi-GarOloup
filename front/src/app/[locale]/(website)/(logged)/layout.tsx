import { ReactNode } from 'react';
import { notFound } from 'next/navigation';

import { getSession } from '@/utils/server/clients';

type Props = {
  children: ReactNode;
};

const LoggedLayout = async ({ children }: Props) => {
  const session = await getSession();
  console.log('session', session);

  if (!session?.user?.token) {
    return <>{children}</>;
  }

  return <>{children}</>;
};

export default LoggedLayout;
