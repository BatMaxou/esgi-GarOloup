import { ReactNode } from 'react';
import { getLocale } from 'next-intl/server';
import { redirect } from '@/i18n/navigation';

import { getApiClient, getSession } from '@/utils/server/clients';
import { paths } from '@/utils/paths';
import { ApiClientError } from '@/lib/api/ApiClientError';
import { PlayerProvider } from '@/contexts/player-context';

type Props = {
  children: ReactNode;
};

const LoggedLayout = async ({ children }: Props) => {
  const session = await getSession();
  const locale = await getLocale();
  const apiClient = await getApiClient();

  if (!session?.user?.token) {
    redirect({ href: paths.home, locale });
  }

  const maybePlayer = await apiClient.player.getCurrent();
  const initialPlayer = maybePlayer instanceof ApiClientError ? null : maybePlayer;

  return <PlayerProvider initialPlayer={initialPlayer}>{children}</PlayerProvider>;
};

export default LoggedLayout;
